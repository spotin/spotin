# Install and configure Visual Studio Code

[Visual Studio Code](../../explanations/about-visual-studio-code/index.md) is an
IDE that we use to develop all our applications.

## Steps

Visual Studio Code can be installed with the following methods.

=== ":simple-linux: Linux"

    You can follow [the official documentation](https://code.visualstudio.com/docs/setup/linux) depending on your Linux distribution.

    For Debian, you can execute the following commands to install Visual Studio Code.

    ```sh
    # Update the available package lists
    sudo apt update

    # Install required packages to add Visual Studio Code's repository
    sudo apt install \
    	ca-certificates \
    	curl \
    	gnupg \
    	lsb-release

    # Add Docker's official GPG key
    sudo mkdir -p /etc/apt/keyrings && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | sudo gpg --dearmor -o /etc/apt/keyrings/packages.microsoft.gpg

    # Set up the repository
    echo \
    "deb [arch=$(dpkg --print-architecture) signed-by=signed-by=/etc/apt/keyrings/packages.microsoft.gpg] https://packages.microsoft.com/repos/code \
    $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/vscode.list > /dev/null

    # Update the available package lists
    sudo apt update

    # Install Visual Studio Code
    sudo apt install code
    ```

=== ":simple-apple: macOS + :simple-windows: Windows"

    Download and install Visual Studio Code from [the official website](https://code.visualstudio.com/). You can then start Visual Studio Code and you should be good to go.

## Summary

Congrats! You have successfully installed and configured Visual Studio Code!

## Versions

At the time of writing this tutorial, the version are as follow (on macOS).

```sh
> code --version
1.74.3
97dec172d3256f8ca4bfb2143f3f76b503ca0534
x64
```
