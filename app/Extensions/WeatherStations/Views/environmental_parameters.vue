<template>
    <div class="height-90">
        <div class="parameter">
<!--            <div class="right_title inline-block">环境参数</div>-->
            <div class="date-address pull-right">
                <div class="datebox divbox">
                    <img src="@/assets/images/dateicon.png" alt="" class="dateicon">
                    <span class="datenum">{{datenum}}</span>
                    <span class="weeknum">{{weeknum}}</span>
                    <span class="timenum">{{timenum}}</span>
                </div>
                <div class="addressbox divbox">
                    <img src="@/assets/images/marker.png" alt="" class="dateicon">
                    <span>{{weatherinfo.name}}</span>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="panels height-80">
            <div class="row height-100">
                <div class="col-md-4 col-sm-4">
                    <div class="panel_list height-100" :class="isfirst==1 ? 'panel_active' : ''">
                        <div class="panel_item panel_item_top">
                            <img src="@/assets/images/temperature.png" alt="" class="panicon">
                            <div class="num_name inline-block num_data">
                                <div class="num_title font-34">{{weatherinfo.temperature}}<span>℃</span></div>
                                <div class="num_des text-left pull-left">{{ $t("COMMON.TEXT15") }}</div>
                            </div>
                        </div>
                        <div class="panel_item panel_item_bottom">
                            <img src="@/assets/images/humidity.png" alt="" class="panicon">
                            <div class="num_name inline-block num_data">
                                <div class="num_title font-34">{{weatherinfo.sd}}<span>RH</span></div>
                                <div class="num_des text-left pull-left">{{ $t("COMMON.TEXT16") }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="panel_list height-100" :class="issecond==1 ? 'panel_active' : ''">
                        <div class="panel_item panel_item_top">
                            <img src="@/assets/images/rain.png" alt="" class="panicon">
                            <div class="num_name inline-block num_data">
                                <div class="num_title font-34">{{weatherinfo.sd}}</div>
                                <div class="num_des text-left pull-left">{{ $t("COMMON.TEXT17") }}</div>
                            </div>
                        </div>
                        <div class="panel_item panel_item_bottom">
                            <img src="@/assets/images/direction.png" alt="" class="panicon">
                            <div class="num_name inline-block num_data">
                                <div class="num_title font-34">{{weatherinfo.wind_power}}</div>
                                <div class="num_des text-left pull-left">{{weatherinfo.wind_direction}}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="panel_list height-100" :class="isthree==1 ? 'panel_active' : ''">
                        <div class="panel_item panel_item_top">
                            <img src="@/assets/images/pressure.png" alt="" class="panicon">
                            <div class="num_name inline-block num_data">
                                <div class="num_title font-34">{{weatherinfo.f1.air_press}}</div>
                                <div class="num_des text-left pull-left">{{ $t("COMMON.TEXT18") }}</div>
                            </div>
                        </div>
                        <div class="panel_item panel_item_bottom">
                            <img src="@/assets/images/ultraviolet.png" alt="" class="panicon">
                            <div class="num_name inline-block num_data">
                                <div class="num_title font-34">{{weatherinfo.f1.ziwaixian}}</div>
                                <div class="num_des text-left pull-left">{{ $t("COMMON.TEXT19") }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped src="@/assets/css/style.css"></style>
<script>
    import $ from 'jquery';

    import {LOGIN, LOGOUT} from "@/core/services/store/auth.module";
    import AUTH from "@/core/services/store/auth.module";
    import ApiService from "@/core/services/api.service";
    import websocket from "@/utils/websocket";
    export default {
        name: 'XEnvironmentalParameters',
        props: {
            loading: {
                type: Boolean,
                default: true,
            },
            legend: {
                type: Boolean,
                default: true,
            },
            apiData: {
                type: Object
            },
            title: {
                type: String,
                default: '',
            },
            fields: {
                type: Object,
            },
            colorStart: {
                type: String,
                default: '#7956EC',
            },
            colorEnd: {
                type: String,
                default: '#3CECCF',
            },
        },
        data() {
            const self = this;
            return {
                chart: null,
                options: {
                    title: {
                        show: false,
                        text: this.title,
                        textStyle: {
                            align: 'center',
                            verticalAlign: 'middle',
                        },
                        top: 10,
                        left: '10',
                    },
                    tooltip: {
                        trigger: 'axis',
                        formatter: function (params) {
                            params = params[0];
                            return params.name;
                        },
                        axisPointer: {
                            animation: false
                        }
                    },
                    xAxis: {
                        type: 'time',
                        splitLine: {
                            show: false
                        },
                        axisLabel:{
                            textStyle: {
                                color: '#a8c5ff'
                            }
                        },
                        axisLine:{
                            lineStyle:{
                                color:'#333333',
                                width:1,//这里是为了突出显示加上的
                            }
                        }
                    },
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, '100%'],
                        splitLine: {
                            show: false
                        },
                        axisLabel:{
                            textStyle: {
                                color: '#a8c5ff'
                            }
                        },
                        axisLine:{
                            lineStyle:{
                                color:'#333333',
                                width:1,//这里是为了突出显示加上的
                            }
                        }
                    },
                    series: [{
                        name: '时序图',
                        type: 'line',
                        showSymbol: false,
                        hoverAnimation: false,
                        data: []
                    }]
                },
                weatherinfo:{},
                datenum:'',
                timenum:'',
                weeknum:'',
                no:0,
                isfirst:1,
                issecond:0,
                isthree:0,
                center: [121.59996, 31.197646],
                lng: 0,
                lat: 0,
                loaded: false,
            };
        },
        computed: {},
        watch: {
            apiData: {
                immediate: true,
                handler(val, oldVal) {
                    var _this = this;
                    if (!_this.loading) {
                        _this.initChart();
                    }
                },
            },
            colorStart() {
                this.initChart();
            },
            colorEnd() {
                this.initChart();
            },
            legend(val, oldVal) {
                this.chart.setOption({
                    legend: {
                        show: val,
                    },
                });
            },
        },
        methods: {
            handleChartClick(param) {
                console.log(param);
            },

            /**
             * echarts instance init event
             * @param {object} chart echartsInstance
             */
            chartInit(chart) {
                this.chart = chart;
                // must resize chart in nextTick
                this.$nextTick(() => {
                    this.resizeChart();
                });
            },

            /**
             * emit chart component init event
             */
            emitInit() {
                if (this.$refs.chart) {
                    this.chart = this.$refs.chart.chart;
                    this.$emit('init', {
                        chart: this.chart,
                        chartData: this.apiData,
                    });
                }
            },

            /**
             * resize chart
             */
            resizeChart() {
                /* eslint-disable no-unused-expressions */
                this.chart && this.chart.resize();
            },

            /**
             * init chart
             */
            initChart() {
                console.log('环境参数');
                console.log(this.apiData);
                this.weatherinfo = this.apiData;
            },
            // 当前时间
            datetime (){
                var date = new Date();
                var year = date.getFullYear() // 年
                var month = date.getMonth() + 1; // 月
                var day  = date.getDate(); // 日
                var hour = date.getHours(); // 时
                var minutes = date.getMinutes(); // 分
                var seconds = date.getSeconds() //秒
                var weekArr = [ '星期天','星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                var week = weekArr[date.getDay()];
                // 给一位数数据前面加 “0”
                if (month >= 1 && month <= 9) {
                    month = "0" + month;
                }
                if (day >= 0 && day <= 9) {
                    day = "0" + day;
                }
                if (hour >= 0 && hour <= 9) {
                    hour = "0" + hour;
                }
                if (minutes >= 0 && minutes <= 9) {
                    minutes = "0" + minutes;
                }
                if (seconds >= 0 && seconds <= 9) {
                    seconds = "0" + seconds;
                }
                //获取id=Date的标签，加入内容。
                this.datenum = year + '-' + month + '-' + day;
                this.timenum = hour + ':' +minutes + ':' + seconds;
                this.weeknum = week;
            },

            actpanel(){
                console.log('面板数量');
                console.log($('.panels'));
            }

        },
        created() {
            this.datetime();
            var _this = this;
            navigator.geolocation.getCurrentPosition(function(data){
                console.log(data)
                var logt = [data.coords.longitude,data.coords.latitude];
                console.log(logt);
                //Push message data to the server and store it in kv
                _this.$emit('send', {
                    logt: logt
                });
            });
        },
        mounted() {
            var _this = this;
            setInterval(this.datetime,1000);

            setInterval(function(){
                if(_this.isfirst == 1){
                    _this.isfirst = 0;
                    _this.issecond = 1;
                    _this.isthree = 0;
                }else if(_this.issecond == 1){
                    _this.isfirst = 0;
                    _this.issecond = 0;
                    _this.isthree = 1;
                }else if(_this.isthree == 1){
                    _this.isfirst = 1;
                    _this.issecond = 0;
                    _this.isthree = 0;
                }
            },6000);

        }
    };
</script>
