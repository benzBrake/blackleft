# AK92-BlackLeft

10 多年前的主题了，放到现在也不过时啊，修正了在 Typecho 1.2 中不能用的问题，并且加了一点私货。不知道还能否向下兼容 IE6，我电脑都是 Win11 了，IE6 也不好搞

## 使用

### 如何在侧边栏显示服务器状态

开机执行 `status/status.sh`，这个脚本是死循环，无需计划任务定期执行。

```shell
cd 主题目录 && nohup status/status.sh -o status/system_usage.json &
```

### 设置静态资源 CDN

填写 `https://jsd.onmicrosoft.cn/gh/benzBrake/blackleft/` 即可，如果你知道别的 github 镜像站，也可以自行填入

## 版权

BlackLeft 主题的原作者已经出国多年，无法联系了。
字体【香萃零度黑】来自：https://github.com/Miiiller/Xiangcui-ZeroHei
