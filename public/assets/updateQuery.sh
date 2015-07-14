#!/bin/bash
TOTALRAM=$(free -m | sed -n 2p | awk '{print $2}')
USEDRAM=$(free -m | sed -n 2p | awk '{print $3}')
RAM=$USEDRAM"/"$TOTALRAM
TOTALHDD=$(df -m | sed -n 2p | awk '{print $2}')
USEDHDD=$(df -m | sed -n 2p | awk '{print $3}')
HDDSPACE=$USEDHDD"/"$TOTALHDD
UPTIME=$(uptime)
LOADAVG=$( echo ${UPTIME#*average:} | cut -c 1-4)
curl --silent --data "secret=$2&Load_AVG=${LOADAVG}&HDD_Space=${HDDSPACE}&RAM=${RAM}&Node=$3" $1