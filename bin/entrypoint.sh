#!/usr/bin/env bash
#
# PHP-Wechaty - Connect ChatBots
#
# https://github.com/wechaty/php-wechaty
#
set -e

export HOME=/bot
export PATH=$PATH:/php-wechaty/bin:/php-wechaty/vendor

function php-wechaty::banner() {
  echo
  figlet " PHP-Wechaty "
  echo ____________________________________________________
  echo "            https://github.com/zhangchunsheng"
}

function php-wechaty::errorBotNotFound() {
  local file=$1
  echo "Container ERROR: can not found bot file: $HOME/$file"

  echo "Container PWD: $(pwd)"
  echo "Container HOME: $HOME"
  echo "Container LS $HOME: $(ls -l $HOME)"

  figlet " Troubleshooting "
  cat <<'TROUBLESHOOTING'
    Troubleshooting:
    1. Did you bind the current directory into container?
      check your `docker run ...` command, if there's no `volumn` arg,
      then you need to add it so that we can bind the volume of /bot:
        `--volume="$(pwd)":/bot`
      this will let the container visit your current directory.
    2. Are you sure your .php files aren't .php.txt?
      this could be a problem on new Windows installs (file
      extensions hidden by default).
    if you still have issue, please have a look at
      https://github.com/wechaty/php-wechaty
      and do a search in issues, that might be help.
TROUBLESHOOTING
}

function php-wechaty::printEnv () {
  num=0.0.5
  echo "PHP-WECHATY Environment Variables: $num"
}

function php-wechaty::errorCtrlC () {
  # http://www.tldp.org/LDP/abs/html/exitcodes.html
  # 130 Script terminated by Control-C  Ctl-C Control-C is fatal error signal 2, (130 = 128 + 2, see above)
  echo ' Script terminated by Control-C '
  figlet ' Ctrl + C '
}

function php-wechaty::pressEnterToContinue() {
  local -i timeoutSecond=${1:-30}
  local message=${2:-'Press ENTER to continue ... '}

  read -r -t "$timeoutSecond"  -p "$message" || true
  echo
}

function php-wechaty::diagnose() {
  local -i ret=$1  && shift
  local file=$1 && shift

  echo "ERROR: Bot exited with code $ret"

  figlet ' BUG REPORT '
  php-wechaty::pressEnterToContinue 30

  echo
  echo "### 1. source code of $file"
  echo
  cat "$HOME/$file" || echo "ERROR: file not found"
  echo

  echo
  echo "### 2. directory structor of $HOME"
  echo
  ls -l "$HOME"

  echo
  echo '### 3. composer.json'
  echo
  cat "$HOME"/composer.json || echo "No composer.json"

  echo
  echo "### 4. directory structor inside $HOME/vendor"
  echo
  ls "$HOME"/vendor || echo "No vendor"

  echo
  echo '### 5. php-wechaty doctor'
  echo
  # php-wechaty-doctor

  figlet " Submit a ISSUE "
  echo _____________________________________________________________
  echo '####### please paste all the above diagnose messages #######'
  echo
  echo 'Wechaty Issue https://github.com/wechaty/php-wechaty/issues'
  echo

  php-wechaty::pressEnterToContinue
}

function php-wechaty::runBot() {
  local botFile=$1

  if [ ! -f "$HOME/$botFile" ]; then
    php-wechaty::errorBotNotFound "$botFile"
    return 1
  fi

  echo  "Working directory: $HOME"
  cd    "$HOME"

  [ -f composer.json ] && {
    # echo "Install dependencies modules ..."

    #
    #
    echo "Please make sure you had installed all the composer modules which is depended on your bot script."
  }


  local -i ret=0
  case "$botFile" in
    *.php)
      echo "Executing php $*"
      php \
        "$@" \
        &
      ;;
    *)
      echo "ERROR: php-wechaty::runBot() no php file"
      exit 1 &
  esac

  wait "$!" || ret=$? # fix `can only `return' from a function or sourced script` error

  case "$ret" in
    0)
      ;;
    130)
      php-wechaty::errorCtrlC
      ;;
    *)
      php-wechaty::diagnose "$ret" "$@"
      ;;
  esac

  return "$ret"
}

function php-wechaty::help() {
  figlet " Docker Usage: "
  cat <<HELP
  Usage: php-wechaty [ mybot.php | COMMAND ]
  Run a PHP <Bot File>, or a <PHP-Wechaty Command>.
  <Bot File>:
    mybot.php: a php program for your bot.
  <Commands>:
    demo    Run PHP-Wechaty DEMO
    doctor  Print Diagnose Report
    test    Run Unit Test
  Learn more at:
    https://github.com/wechaty/wechaty/wiki/Docker
HELP
}

function main() {
  # issue #84
  echo -e 'nameserver 114.114.114.114\nnameserver 1.1.1.1\nnameserver 8.8.8.8' >> /etc/resolv.conf > /dev/null

  php-wechaty::banner
  figlet Connecting
  figlet ChatBots

  php-wechaty::printEnv

  VERSION=$(echo '0.0.5' || echo '0.0.0(unknown)')

  echo
  echo -n "Starting Docker Container for PHP-Wechaty v$VERSION with "
  echo -n "php $(php --version) ..."
  echo

  local -i ret=0

  local defaultArg=help

  case "${1:-${defaultArg}}" in
    #
    # 1. Get a shell
    #
    shell | sh | bash)
      /bin/bash -s || ret=$?
      ;;

    #
    # 2. Run a bot
    #
    *.php)
      php-wechaty::runBot "$@" || ret=$?
      ;;

    #
    # 3. If there's additional `npm` arg...
    #
    php)
      composer install
      "$@" || ret=$?
      ;;

    help|version)
      php-wechaty::help
      ;;

    #
    # 4. Default to execute php ...
    #
    *)
      php "$@" || ret=$?
     ;;
  esac

  php-wechaty::banner
  figlet " Exit $ret "
  return $ret
}

main "$@"
