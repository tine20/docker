phpinfo=$(docker run tine20:latest sh -c "php -info")

for i in igbinary xdebug yaml sockets pcntl intl bcmath; do
  if [[ $phpinfo != *"$i"* ]]; then
    echo "php module $i is missing!"
    exit 1
  fi;
done
