#!/bin/bash

set -e

prj_path=$(cd $(dirname $0); pwd -P)
SCRIPTFILE=`basename $0`

base_do_init=0
devops_prj_path="$prj_path/devops"

source $devops_prj_path/base.sh

nginx_port=11983
app="cube-app-server.liaohuqiu.net"

php_image=liaohuqiu/gems-php
nginx_image=nginx:1.11


nginx_container=$app.nginx
php_container=$app.php

app_storage_path="/opt/data/$app/data"
logs_dir="$app_storage_path/logs"

app_writable_path=$app_storage_path/writable
src_path_in_docker='/opt/src'

function stop_php() {
    stop_container $php_container
}

function run() {
    run_php
    run_nginx
}

function stop() {
    stop_nginx
    stop_php
}

function restart() {
    stop
    run
}

function clean() {
    stop
}

function stop_nginx() {
    stop_container $nginx_container
}

function run_nginx() {

    local nginx_data_dir="$devops_prj_path/nginx-data"
    local nginx_log_path="$logs_dir/nginx"

    args="--restart=always"

    args="$args -p $nginx_port:80"

    # nginx config
    args="$args -v $nginx_data_dir/conf/nginx.conf:/etc/nginx/nginx.conf"

    # for the other sites
    args="$args -v $nginx_data_dir/conf/extra/:/etc/nginx/extra"

    # nginx certificate
    args="$args -v $nginx_data_dir/ssl-cert/:/etc/nginx/ssl-cert"

    # logs
    args="$args -v $nginx_log_path:/var/log/nginx"

    # generated nginx docker sites config
    args="$args -v $devops_prj_path/templates/nginx/:/etc/nginx/docker-sites"

    args="$args --link $php_container:php"

    args="$args -v $prj_path:$src_path_in_docker"

    run_cmd "docker run -d $args --name $nginx_container $nginx_image"
}

function _run_php_container() {
    local cmd=$1
    local args="$args --restart always"

    args="$args -v $logs_dir:/var/log/php"
    args="$args -v $devops_prj_path/docker/php/conf/php-dev.ini:/usr/local/etc/php/php.ini"
    args="$args -v $devops_prj_path/docker/php/conf/php-fpm.conf:/usr/local/etc/php-fpm.conf"

    args="$args -v $app_writable_path:$src_path_in_docker/writable/"
    args="$args -v $prj_path:$src_path_in_docker"

    args="$args -w $src_path_in_docker"
    run_cmd "docker run -d $args --name $container_name $php_image bash -c '$cmd'"
}

function run_php() {
    stop_container $php_container
    local cmd="bash manager.sh run_php_"
    _run_php_container "$cmd"
}

function run_php_() {
    if [ -f /var/log/php/php-fpm-error.log ]; then
        run_cmd 'touch /var/log/php/php-fpm-error.log'
    fi
    if [ -f /var/log/php/php-fpm-slow ]; then
        run_cmd 'touch /var/log/php/php-fpm-slow.log'
    fi
    run_cmd 'chmod -R a+r /var/log/php/'
    run_cmd '/usr/local/sbin/php-fpm -R'
}

function to_php() {
    local cmd='bash'
    _send_cmd_to_php_container "$cmd"
}

function _send_cmd_to_php_container() {
    local cmd=$1
    run_cmd "docker exec -i $php_container bash -c '$cmd'"
}

function help() {
	cat <<-EOF
    
    Usage: mamanger.sh [options]

	    Valid options are:

            run
            stop
            restart

            run_nginx
            stop_nginx
            restart_nginx

            run_php
            to_php
            stop_php

            -h                      show this help message and exit

EOF
}

ALL_COMMANDS="run stop restart clean"
ALL_COMMANDS="$ALL_COMMANDS run_nginx stop_nginx restart_nginx"
ALL_COMMANDS="$ALL_COMMANDS run_php run_php_ to_php stop_php restart_php"

action=${1:-help}
list_contains ALL_COMMANDS "$action" || action=help
$action "$@"
