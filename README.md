# Change Wallpaper MacOS 

This PHP script allows you to change your wallpaper on the Mac. Wallpapers are 
downloaded from wallhaven.cc.

## Getting Started

To run this script you need to have PHP7 installed on your local machine. As 
said this script make use of wallhaven.cc. To find more information about their
api visit: https://wallhaven.cc/help/api
 
### Requirements

* MacOS
* Xcode
* [Homebrew macos-wallpaper](https://formulae.brew.sh/formula/wallpaper)
* PHP 7+

### Installing

First we need to be sure to install the wallpaper script for MacOs. This script
can be download with brew. [Homebrew macos-wallpaper](https://formulae.brew.sh/formula/wallpaper)

brew install wallpaper 

Visit https://wallhaven.cc/ and create you own account. When done go to your
profile and there you can find your API_KEY. You need to fill it in the head 
of the script.

After installing the wallpaper script you can run the Change Wallpaper MacOS 
PHP script on a local PHP installment and run it within the browser or via CLI 
if you wish. 

In the browser you have to open the following url:
http://{{script-path}}/?topRange=1M
http://{{script-path}}/?q=landscape
 
| Type         | url variable   | values                      |
|--------------|----------------|-----------------------------|
| search term  | q              | beach                       |
| Top 24       | topRange       | 1d, 3d, 1w, 1M*, 3M, 6M, 1y |
| refresh time | interval (sec) | 900                         |

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) 
for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* **Jan Valkenburg** - *Initial work* - [Github](https://github.com/JanValkenburg/mac-wallhaven-change-wallpaper)

See also the list of [contributors](https://github.com/your/project/contributors) 
who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) 
file for details


