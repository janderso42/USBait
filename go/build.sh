#! /bin/bash

if [ "$1" == "" ]; then
	echo "Missing name of new output directory"
	exit;
fi

env GOOS=windows GOARCH=amd64 go build -o ~/${1}/win64.exe
if [ $? == 0 ]; then 
	echo "win64 created"
fi

env GOOS=windows GOARCH=386 go build -o ~/${1}/win32.exe
if [ $? == 0 ]; then 
	echo "win32 created" 
fi
	
env GOOS=darwin GOARCH=amd64 go build -o ~/${1}/mac64
if [ $? == 0 ]; then 
	echo "mac64 created" 
fi

env GOOS=darwin GOARCH=386 go build -o ~/${1}/mac32
if [ $? == 0 ]; then 
	echo "mac32 created"
fi

env GOOS=linux GOARCH=amd64 go build -o ~/${1}/lin64
if [ $? == 0 ]; then 
	echo "linux64 created"
fi

env GOOS=linux GOARCH=386 go build -o ~/${1}/lin32
if [ $? == 0 ]; then 
	echo "linux 32 created"
fi
