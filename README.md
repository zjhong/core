# thingspanel-php

#### 介绍
ThingsPanel PHP Core

#### 软件架构
![Structure](https://aliossdownloads.tower.im/859770/b487884aba1a48a13396851d98f0c044?Expires=1582475147&OSSAccessKeyId=LTAIxLlUqJXhFTHz&Signature=SZy8u4MDQc9RWwFiEkDoIGQ4dhE%3D&response-content-disposition=inline%3Bfilename%3D%22image.png%22&response-content-type=image%2Fpng)


#### 安装教程

1.  操作命令：`php bin/laravels {start|stop|restart|reload|info|help}`
2.  xxxx
3.  xxxx

#### 生产环境
[查看](https://github.com/hhxsv5/laravel-s/blob/master/README-CN.md)

#### 使用说明

1.  xxxx
2.  xxxx
3.  xxxx

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
