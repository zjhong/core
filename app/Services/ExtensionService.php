<?php

namespace App\Services;


use App\Models\Assets\Asset;
use App\Extensions\BaseInterface;
use App\Models\Panels\Widget;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExtensionService
{
    /**
     * Init config
     */
    public function init()
    {
        $config = [];

        $extensions = Storage::disk('extension')->directories();
        foreach ($extensions as $extension) {
            if (Storage::disk('extension')->exists($extension . '/config.php')) {
                $config[strtolower($extension)] = include app_path('Extensions/' . $extension . '/config.php');

                //parse views
                $views = Storage::disk('extension')->allFiles($extension . '/Views/');
                if (!empty($views)) {
                    foreach ($views as $view) {
                        File::copy(Storage::disk('extension')->path($view), Storage::disk('vue')->path('src/components/charts/' . $this->getTemplateName($extension, basename($view))));
                    }
                }
            }
        }

        Storage::disk('local')->put('extensions.php', '<?php //auto generated' . chr(10) . 'return ' . var_export($config, true) . ';');

        Log::info('Cache extension config successful!');
    }

    /**
     * @param array $data comes from websocket.send(data)
     * @param bool $initial
     * @return string
     * @throws BindingResolutionException
     */
    public function handle(array $data, $initial = false)
    {
        //process cache
        if (Cache::has('WID_' . $data['wid'])) {
            $widget = Cache::get('WID_' . $data['wid']);
        } else {
            /** @var Widget $widget */
            $widget = Widget::find($data['wid']);
            Cache::put('WID_' . $widget->id, $widget, 600);
        }

        /** @var BaseInterface $abstract */
        $abstract = app()->make($this->getExtensionAction($widget->widget_identifier));
        if ($initial) {
            $this->setWidgetConfig($widget, $data['config']);
        }

        $responseData = $abstract->main([$widget->device_id], $data, $this->getWidgetFields($widget->widget_identifier), $initial); //extension only know assets, widget and custom data config

        //TODO RPC for Python, Java

        //response json data
        return json_encode([
            'wid' => $data['wid'],
            'data' => $responseData
        ]);
    }

    /**
     * @param Widget $widget
     * @param array $config
     */
    public function setWidgetConfig(Widget $widget, array $config): void
    {
        $widget->update([
            'config' => array_merge($widget->config, $config)
        ]);

        //cache widget config
        Cache::put('WID_' . $widget->id, $widget, 3600);
    }

    /**
     * @param string $widgetName
     * @return mixed|string
     */
    public function getExtensionInfo($widgetName = '')
    {
        $widgetName = strtolower($widgetName);
        $widgets = include storage_path('app/extensions.php');
        if ($widgetName) {
            if (isset($widgets[$widgetName])) {
                return $widgets[$widgetName];
            } else {
                return '';
            }
        } else {
            return $widgets;
        }
    }

    /**
     * @param $action
     * @return mixed
     */
    public function getExtensionAction($widgetIdentifier)
    {
        list($widgetName, $widgetSubKey) = explode(':', $widgetIdentifier);
        $info = $this->getExtensionInfo($widgetName);
        return $info['widgets'][$widgetSubKey]['class'];
    }

    /**
     * get widget fields
     * @param $widgetIdentifier
     * @return mixed
     */
    public function getWidgetFields($widgetIdentifier)
    {
        list($widgetName, $widgetSubKey) = explode(':', $widgetIdentifier);
        $widgetInfo = $this->getExtensionInfo($widgetName);
        return isset($widgetInfo['widgets'][$widgetSubKey]['fields']) ? $widgetInfo['widgets'][$widgetSubKey]['fields'] : [];
    }

    /**
     * parse vue template name
     * @param $extensionName
     * @param $templateName
     * @return string
     */
    public function getTemplateName($extensionName, $templateName)
    {
        if (substr($templateName, 0, 2) == 'x_') {
            return $templateName;
        }

        // rewrite name, e.g. extension1:template1 to extension1_template1
        if (strpos($templateName, ':') > -1) {
            list($extensionName, $templateName) = explode(':', $templateName);
        }
        return strtolower($extensionName . '_' . $templateName);
    }
}
