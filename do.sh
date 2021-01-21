#!/bin/sh

cat $1 | while read line
do
    curl -d "cookie=${line}" -sL "http://localhost/byuHealthReport/index.php?action=$2"
done