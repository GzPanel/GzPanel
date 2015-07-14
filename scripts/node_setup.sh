#!/bin/bash
# This file acts as an 'installer' for linux systems.
# It will use values provided by the website to set-up a 2-way connection.
DEBUG=0
# Please do not change of the code below, as it may break the installation and ruin your system.

SECRETKEY=""
WEBSITEURL=""
USERNAME="PanelAgent"



# Find IP of server
NODEHOST=$(curl http://myip.dnsomatic.com 2>/dev/null)

if [ $(id -u) -eq 0 ]; then
	if [[ "$1" = "uninstall" ]]; then
		# User would like to uninstall
		userdel PanelAgent
		rm -rf /home/PanelAgent

		curl -X  "DELETE" --data "Host=${NODEHOST}&secret=${SECRETKEY}" ${WEBSITEURL}"api/v1/nodes"
		echo "Successfully uninstalled this script!"
		if [ $DEBUG = 0 ]; then
			echo "This script is now getting deleted."
			rm -- "$0"
		fi
	else
		# Generate a random password
		PASSWORD=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)
		# Find the value of the SSH port
		for mLine in `grep 'Port ' /etc/ssh/sshd_config`
		do
		  NODEPORT=${mLine#* }
		done


		# Will use current directory for agent.
		HOSTDIRECTORY=$( pwd )

		echo "Please enter a name to identify this node (Leave empty to generate one):"
		read NODE_NAME
		if [[ -z "$NODE_NAME" ]]; then
			NODE_NAME="Node-"$(cat /dev/urandom | tr -dc '0-9' | fold -w 3 | head -n 1)
		fi

		echo "Directory for server files: (leave empty for default: /home/PanelAgent)"
		read HOSTDIRECTORY
		if [[ -z "$HOSTDIRECTORY" ]]; then
			HOSTDIRECTORY="/home/PanelAgent/"
		fi


		egrep "^$USERNAME" /etc/passwd >/dev/null
		if [ $? -eq 0 ]; then
			echo "$USERNAME exists! Please restart setup AFTER deleting this user account.."
			exit
		else
			pass=$(perl -e 'print crypt($ARGV[0], "PASSWORD")' $PASSWORD)
			useradd -m -p $pass $USERNAME

			if [ $? -eq 0 ]; then
				echo "Added user account..."
				# Creating the status data folder to host the status request
				installedCronie=0

				if ! rpm -q cronie &> /dev/null; then
					installedCronie=1
					yum install cronie
				fi

				mkdir -p /home/PanelAgent/status/{data,scripts}
				wget -P /home/PanelAgent/status/scripts/ ${WEBSITEURL}/assets/updateQuery.sh >/dev/null 2>&1
				chmod 755 /home/PanelAgent/status/scripts/updateQuery.sh
				PINGSECRET=$(curl --silent --data "Name=${NODE_NAME}&Host=${NODEHOST}&Port=${NODEPORT}&Password=${PASSWORD}&Directory=${HOSTDIRECTORY}&OS=Linux&secret=${SECRETKEY}" ${WEBSITEURL}"api/v1/nodes")
				if [[ $(uname -m) == "x86_64" ]]; then
					# 64 Bit
					wget http://stedolan.github.io/jq/download/linux64/jq
				else
					# 32 Bit
					wget http://stedolan.github.io/jq/download/linux32/jq
				fi
				chmod +x ./jq
				cp jq /usr/bin
				rm -rf jq
				NODEID=$( echo $PINGSECRET | jq '.results.Node' | sed "s/\"//g")
				if  [[  $( echo $PINGSECRET | jq '.status' ) = *"40"* ]]; then
					echo "Failed installation - Reverting changes"
					echo "Please regenerate a new script from "$WEBSITEURL
					userdel PanelAgent
					rm -rf /home/PanelAgent
					rm -rf /usr/bin/jq
					if [ $installedCronie = 1 ]; then
						yum remove cronie
					fi
				else
					WEBSITEURL+="api/v1/nodes_status"
					line='*/5 * * * * /home/PanelAgent/status/scripts/updateQuery.sh '$WEBSITEURL' '$SECRETKEY' '$NODEID
					(crontab -u PanelAgent -l; echo "$line" ) | crontab -u PanelAgent -
					service crond restart

					if [ $DEBUG = 0 ]; then
						echo "This script is now getting deleted."
						rm -- "$0"
					fi
					# We will send an update ping - The cron will run after 5 minutes.
					STATUSREQUEST=$(/home/PanelAgent/status/scripts/updateQuery.sh $WEBSITEURL $SECRETKEY $NODEID)
					# We must now generate the shell to ping the panel every 5 minutes, so that it can be run by the cron-tab above.
					echo "======================================"
					echo "Node ID is "$NODEID" and Name is "$NODE_NAME
					echo "Successful installation - Check your web-panel now"
					echo "======================================"
				fi
			else
				echo "Failed to add user... Restart set-up please."
				exit
			fi
		fi
	fi
else
	echo "Please execute this script as 'root'."
	echo "Exiting set-up..."
	exit
fi