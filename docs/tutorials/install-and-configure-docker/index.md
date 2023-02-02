# Install and configure Docker

[Docker](../../explanations/about-docker/index.md) allows to create containers that are independent from your operating system. This allows to embed all your software needs and ship it to someone else.

In this tutorial, you will install Docker in order to develop, build and ship your applications so that will work on other Dockers environments.

## Steps

Docker can be installed with the following methods.

=== ":simple-linux: Linux"

	You can follow [the official documentation](https://docs.docker.com/engine/install/) depending on your Linux distribution.

	You might want to allow non-root users to run Docker following [the post-install documentation](https://docs.docker.com/engine/install/linux-postinstall/).

	The Compose plugin has to be installed externally to work. You can install it with your package manager with [the related documentation](https://docs.docker.com/compose/install/linux/).

	For Debian, you can execute the following commands to install Docker with the Compose plugin.

	```sh
	# Uninstall old versions
	sudo apt remove docker docker-engine docker.io containerd runc

	# Update the available package lists
	sudo apt update

	# Install required packages to add Docker's repository
	sudo apt install \
		ca-certificates \
		curl \
		gnupg \
		lsb-release

	# Add Docker's official GPG key
	sudo mkdir -p /etc/apt/keyrings && curl -fsSL https://download.docker.com/linux/debian/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

	# Set up the repository
	echo \
	"deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian \
	$(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

	# Update the available package lists
	sudo apt update

	# Install Docker and the Compose plugin
	sudo apt install docker-ce docker-ce-cli containerd.io docker-compose-plugin

	# Create the `docker` group (it might already exist)
	sudo groupadd docker

	# Add your user to the `docker` group
	sudo usermod -aG docker $USER

	# Activate the changes to group
	newgrp docker
	```

=== ":simple-apple: macOS"

	Download and install Docker Desktop from [the official website](https://www.docker.com/). You can then start Docker Desktop and you should be good to go.

=== ":simple-windows: Windows"

	On Windows, it is highly recommended to install a WSL distribution. You can follow [the official documentation](https://learn.microsoft.com/en-us/windows/wsl/install) to install your Linux distribution.

	For the default Ubuntu WSL distribution, you can execute the following steps.

	Press the ++windows++ key. Search for **Powershell**. Right-click on it, and select **Run as administrator**. Type the following commands.

	```sh
	# Install Ubuntu WSL
	wsl --update --install --distribution Ubuntu

	# Set Ubuntu as the default WSL
	wsl --setdefault Ubuntu
	```

	Once the WSL has been installed, download and install Docker Desktop from [the official website](https://www.docker.com/). Restart your computer. You can then start Docker Desktop and you should be good to go.

## Summary

Congrats! You have successfully installed and configured Docker!

## Versions

At the time of writing this tutorial, the versions for Docker and Docker Compose are as follow (on macOS).

```sh
> docker --version
Docker version 20.10.21, build baeda1f

> docker compose version
Docker Compose version v2.13.0
```
