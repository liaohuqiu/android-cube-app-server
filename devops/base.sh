#!/bin/bash

NC='\033[0m'      # Normal Color
RED='\033[0;31m'  # Error Color
CYAN='\033[0;36m' # Info Color

CONFIG_SERVER='http://config.dev.jiudouyu.com.cn'
DOCKER_DOMAIN='docker-registry.sunfund.com'

# base_do_init init_config_by_developer_name / load_config / do_init / devops_prj_path

function run_cmd() {
    t=`date`
    echo "$t: $1"
    eval $1
}

function ensure_dir() {
    if [ ! -d $1 ]; then
        run_cmd "mkdir -p $1"
    fi
}

function stop_container() {
    container_name=$1
    cmd="docker ps -a -f name='^/$container_name$' | grep '$container_name' | awk '{print \$1}' | xargs -I {} docker rm -f --volumes {}"
    run_cmd "$cmd"
}

function push_image() {
    local image_name=$1
    url=$DOCKER_DOMAIN/$image_name
    run_cmd "docker tag $image_name $url"
    run_cmd "docker push $url"
}

function docker0_ip() {
    local host_ip=$(ip addr show docker0 | grep -Eo 'inet (addr:)?([0-9]*\.){3}[0-9]*' | grep -Eo '([0-9]*\.){3}[0-9]*' | grep -v '127.0.0.1' | awk '{print $1}' | head  -1)
    echo $host_ip
}

function pull_image() {
    local image_name=$1
    url=$DOCKER_DOMAIN/$image_name
    run_cmd "docker pull $url"
    run_cmd "docker tag $url $image_name"
}

function render_local_config() {
    local config_key=$1
    local template_file=$2
    local config_file=$3
    local out=$4

    shift
    shift
    shift
    shift

    local config_type=yaml
    cmd="curl -s -F 'template_file=@$template_file' -F 'config_file=@$config_file' -F 'config_key=$config_key' -F 'config_type=$config_type'"
    for kv in $*
    do
        cmd="$cmd -F 'kv_list[]=$kv'"
    done
    cmd="$cmd $CONFIG_SERVER/render-config > $out"
    run_cmd "$cmd"
    head $out && echo
}

function read_kv_config() {
    local file=$1
    local key=$2
    cat $file | grep "$key=" | awk -F '=' '{print $2}'
}

function render_server_config {
    local config_key=$1
    local template_file=$2
    local config_file_name=$3
    local out=$4

    shift
    shift
    shift
    shift

    cmd="curl -s -F 'template_file=@$template_file' -F 'config_key=$config_key' -F 'config_file_name=$config_file_name'"
    for kv in $*
    do
        cmd="$cmd -F 'kv_list[]=$kv'"
    done
    cmd="$cmd $CONFIG_SERVER/render-config > $out"
    run_cmd "$cmd"
    head $out && echo
}

function list_contains() {
    local var="$1"
    local str="$2"
    local val

    eval "val=\" \${$var} \""
    [ "${val%% $str *}" != "$val" ]
}

manager_config_file="$devops_prj_path/auto-gen.developer-name.config"
function _try_load_config() {
    if [ ! -f "$manager_config_file" ]; then
        echo 'Config file is not found, please call `sh manager.sh init developer_name` first.'
        exit 1
    fi
    source $manager_config_file
    load_config $developer_name
}

function init() {
    echo "developer_name: $developer_name"
    echo "developer_name=$developer_name" > $manager_config_file
    do_init $developer_name
}

action=${1:-help}
if [ $base_do_init != 0 ]; then
    if [ "$action" = 'init' ]; then
        if [ $# -lt 2 ]; then
            echo "Usage sh $0 init developer_name";
            exit 1
        fi
        developer_name=$2
        init_config_by_developer_name $developer_name
        init $developer_name
        exit 0
    else
        _try_load_config
        init_config_by_developer_name $developer_name
    fi
fi
