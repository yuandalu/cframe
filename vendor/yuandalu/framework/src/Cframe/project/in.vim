set nu
noremap <F8> <Esc>:! $FW_HOME/project/autoload_builder.sh <CR> <ESC>:cs kill -1 <CR> <ESC>:cs add $FW_HOME/project/cscope.out <CR>
