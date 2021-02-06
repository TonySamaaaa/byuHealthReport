#!/bin/sh

cat $2 | while read line
do
    curl -d "AuthToken=${line}" -sL "http://localhost/byuHealthReport/test/do.php?func=$1"
done
