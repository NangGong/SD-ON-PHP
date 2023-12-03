## 该代码是配合toolbox插件使用的

### 安装
- 把整个文件放到站点根目录
- 设置伪静态
```
location / {
    if (!-e $request_filename){
        rewrite ^(.*)$ /index.php?s=$1 last;
        break;
    }
}
```
- 设置运行目录public
- 申请ssl证书强制开启https
  
### 配置dalle

- 打开api/dalle目录打开config.json
```
   {
  "domain": "api.openai.com",
  "key": [
    "随便填，COW项目中的dalle的key填这个"
  ],
  "openai_key": [
      "这个填写官方openai的key"
  ]
}
```
- domain是openai官方api的域名，也可以填反向代理中转
- key是你自己设置的key，也就是插件配置中你填的key，可以随便设置
- openai_key是你真实的openai官方的key，也可以是中转的key


