#!/bin/ash

output_path=""
script_dir=$(dirname "$(readlink -f "$0")")

while getopts "o:" opt; do
    case $opt in
        o)
            output_path=$OPTARG
            ;;
        *)
            ;;
    esac
done

if [ -z "$output_path" ]; then
    output_path="$script_dir/system_usage.json"
fi

while true; do
    # 获取内存总量和占用量
    mem_total=$(free -m | awk '/Mem/ {printf "%.2f", $2}')
    mem_used=$(free -m | awk '/Mem/ {printf "%.2f", $3}')
    mem_usage=$(free -m | awk '/Mem/ {usage = $3 / $2 * 100; printf "%.2f", usage}')

    # 获取CPU占用百分比
    cpu_usage=$(grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}')

    # 获取根目录占用百分比
    disk_usage=$(df -h / | awk '/\// {usage = $(NF-1); print usage}')

    # 获取硬盘总容量和已占用空间
    disk_total=$(df -h / | awk '/\// {print $2}')
    disk_used=$(df -h / | awk '/\// {print $3}')

    # 构建JSON对象
    json=$(printf '{"cpu_usage": "%s%%", "mem_total": "%sMB", "mem_used": "%sMB", "mem_usage": "%s%%", "disk_usage": "%s", "disk_total": "%s", "disk_used": "%s"}' "$cpu_usage" "$mem_total" "$mem_used" "$mem_usage" "$disk_usage" "$disk_total" "$disk_used")

    # 将JSON对象写入文件
    printf "%s" "$json" > "$output_path"

    sleep 1
done