 #!/bin/sh

PROCESS_NUM=$(ps -ef | grep get_sensor_v1 | grep -v "grep" | wc -l)

if [ $PROCESS_NUM -eq 1 ]
then 
	exit 1
else
	php /home/pi/DashScreen/PiHomeDashScreen/sensor/get_sensor_v1.php
fi
	exit 0