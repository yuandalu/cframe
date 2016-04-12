PROJECT_HOME=`pwd`
noremap <F8> <Esc>:! $PROJECT_HOME/project/autoload_builder.sh <CR>
noremap <F9> <Esc>:w! <CR> <Esc>:! /usr/local/bin/php -f "%" <CR>

