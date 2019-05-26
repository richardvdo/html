#!/usr/bin/python

import RPi.GPIO as GPIO
import time
import datetime

compteur_principal=0.0


def cb_compteur_principal(channel):
	global compteur_principal
	now = datetime.datetime.now()
	heure = now.strftime('%Y-%m-%d %H:%M:%S.%f')
	compteur_principal=compteur_principal+1
	print "compteur principal - %s : %s kW" % (heure,compteur_principal)

GPIO.setmode(GPIO.BCM)
GPIO.setup(17, GPIO.IN, pull_up_down=GPIO.PUD_UP)
GPIO.add_event_detect(17, GPIO.FALLING, callback=cb_compteur_principal, bouncetime=70)

while True:
	time.sleep(0.1)