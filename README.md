### 产品介绍

- 面向快速实施的物联网方案开发与共享平台，以简单、快速、美观、通用为特点。
- 技术研发人员可以快速构建应用，并将业务打包分发给世界各地的用户以获取收入。
- 对业务人员而言，ThingsPanel不用写代码，一整套方案开箱即用。业务交付时间是传统的物联网方案的30%以下。成本也大幅度降低。
- 可广泛应用于交通、医疗、消费、家居、消防、安防、工业、农业等各个领域。

### 产品截图
![可视化界面截图](https://raw.githubusercontent.com/universe-hsh/assets/master/images/demo.png "Thingspanel-Dashboard.png")

### 功能结构图

![功能结构图](https://raw.githubusercontent.com/universe-hsh/assets/master/images/structure.png "structure.png")

## 目录说明

```
├── conf (配置文件目录)
│   ├── core (系统配置)
│   ├── vue (前端配置)
├── data
│   └── rabbitmq (rabbitmq数据目录)
│   └── timescaledb (timescaledb数据库)
│   ├── core (运行日志)
├── logs
│   ├── core (运行日志)
│   └── nginx (nginx log文件目录)
│   └── supervisor (log文件目录)
└── entensions (插件目录)
└── .env (docker配置)

## 如何使用？

下载代码到本地，（项目安装代码地址：https://github.com/ThingsPanel/docker.git）

进入到项目根目录直接执行以下命令即可启动

生产模式: `docker-compose up -d`

开发模式: `docker-compose --env-file .env.dev up`

如果timescaledb启动不了, 可删除data/timescaledb/pg_tblspc/.gitkeep后重试

## 如何进入容器

第一种方法: 

```bash
# 查看所有容器
docker ps -a
# 进入容器
docker exec -it thingspanel-xxx bash
```

## 前台访问
`http://127.0.0.1:8080`

默认登录账号：admin@protonmail.com 密码：admin@protonmail.com

## 数据推送
MQTT Port: 1883\
TCP Port: 9505

示例格式：
```json
{
    "token":"设备编号", //设备唯一标识符, 从业务后台生成
    "ts":1451649600512, //可选，单位毫秒
    "values":{
        "temperature": "30", //温度
        "humidity": "85" //湿度
        ... 更多参数
    }
}
```

#### 技术标准
##### 1. 编码 `UTF-8`
##### 2. 数据格式 `json`
##### 3. API Response structure
```json
{
  "code": 200,
  "message": "message content",
  "data": {
    "field1": 1,
    "field2": 2
  }
}
```

#### 4. Code list
| Code       | Note    |
| --------   | -----:  |
| 200        | Success      |
| 401        | Unauthorized      |
| 500        | System error     |

#### supervisor configuration
/etc/supervisord.d/thingspanel.ini
```bash
[program:thingspanel]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/backend/artisan telemetry:consumer
autostart=true
autorestart=true
user=apache
numprocs=4
redirect_stderr=true
stderr_logfile=/var/log/supervisor/thingspanel.log
stdout_logfile=/var/log/supervisor/thingspanel.log
```
/etc/supervisord.d/tcp_server.ini
```bash
[program:tcp_server]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/backend/artisan telemetry:tcp_server
autostart=true
autorestart=true
user=apache
numprocs=1
redirect_stderr=true
stderr_logfile=/var/log/supervisor/tcp_server.log
stdout_logfile=/var/log/supervisor/tcp_server.log
```
