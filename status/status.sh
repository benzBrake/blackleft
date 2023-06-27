#!/bin/bash

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

while true; do
    # 获取CPU占用百分比
    cpu_usage=$(grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage "%"}')

    # 获取内存占用百分比
    mem_usage=$(free -m | awk '/Mem/ {usage = $3 / $2 * 100; printf "%.2f%", usage}')

    # 获取根目录占用百分比
    disk_usage=$(df -h / | awk '/\// {usage = $(NF-1); print usage}')

    # 构建JSON对象
    json='{ "cpu_usage": "'$cpu_usage'", "mem_usage": "'$mem_usage'", "disk_usage": "'$disk_usage'" }'

    # 将JSON对象写入文件
    echo "$json" > "$script_dir/system_usage.json"

    sleep 1
done