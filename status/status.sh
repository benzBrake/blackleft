#!/bin/bash
while true; do
    # 获取CPU占用百分比
    cpu_usage=$(grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage "%"}')

    if [ -f /sys/fs/cgroup/memory.current ]; then
      # 获取内存总量和已用内存，并转换为MB
      mem_total=$(cat /sys/fs/cgroup/memory.max)
      mem_used=$(cat /sys/fs/cgroup/memory.current)
      mem_total=$(echo "scale=2; $mem_total / (1024 * 1024)" | bc)
      mem_used=$(echo "scale=2; $mem_used / (1024 * 1024)" | bc)

      # 获取swap总量和已用swap，并转换为MB
      swap_total=$(cat /sys/fs/cgroup/memory.swap.max)
      swap_used=$(cat /sys/fs/cgroup/memory.swap.current)
      swap_total=$(echo "scale=2; $swap_total / (1024 * 1024)" | bc)
      swap_used=$(echo "scale=2; $swap_used / (1024 * 1024)" | bc)
    else
      # 获取内存总量和占用量
      mem_total=$(free -m | awk '/Mem/ {printf "%.2f", $2}')
      mem_used=$(free -m | awk '/Mem/ {printf "%.2f", $3}')
      mem_usage=$(free -m | awk '/Mem/ {usage = $3 / $2 * 100; printf "%.2f", usage}')

      # 获取swap总量和已用swap
      swap_total=$(free -m | awk '/Swap/ {printf "%.2f", $2}')
      swap_used=$(free -m | awk '/Swap/ {printf "%.2f", $3}')
      swap_usage=$(free -m | awk '/Swap/ {usage = $3 / $2 * 100; printf "%.2f", usage}')
    fi

    # 计算内存占用百分比
    mem_usage=$(echo "scale=2; $mem_used / $mem_total * 100" | bc)
    mem_usage="${mem_usage}%"

    # 计算swap占用百分比
    swap_usage=$(echo "scale=2; $swap_used / $swap_total * 100" | bc)
    swap_usage="${swap_usage}%"

    # 获取根目录占用百分比
    disk_total=$(df -h / | awk '/\// {print $2}')
    disk_used=$(df -h / | awk '/\// {print $3}')

    # 构建JSON对象
    json='{ "cpu_usage": "'$cpu_usage'", "mem_usage": "'$mem_usage'", "mem_total": "'$mem_total' MB", "mem_used": "'$mem_used' MB", "swap_usage": "'$swap_usage'", "swap_total": "'$swap_total' MB", "swap_used": "'$swap_used' MB", "disk_usage": "'$disk_usage'" }'

    # 将JSON对象写入文件
    echo "$json" > /srv/usr/themes/blackleft/status/system_usage.json

    sleep 1
done