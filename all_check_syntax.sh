tree -i -f | awk '/.*\.php|.*\.phtml/' | awk '{print $1}' | xargs -n1 /usr/local/opt/php70/bin/php -l | grep 'PHP Fatal error'
