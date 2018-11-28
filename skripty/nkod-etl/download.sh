#!/usr/bin/env bash
cd /opt/isds/dist
java -DconfigurationFile=./configuration.properties -jar isds-0.0.0.jar >> /data/isds/log.txt