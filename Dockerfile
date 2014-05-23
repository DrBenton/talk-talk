# DOCKER-VERSION 0.9
FROM    former03/dev03-debian-squeeze:latest
MAINTAINER Olivier Philippon <olivier@rougemine.com>

# Dotdeb.org
RUN echo "deb http://packages.dotdeb.org squeeze all" > /etc/apt/sources.list.d/dotdeb.list
RUN echo "deb-src http://packages.dotdeb.org squeeze all" >> /etc/apt/sources.list.d/dotdeb.list
RUN wget http://www.dotdeb.org/dotdeb.gpg
RUN sudo apt-key add dotdeb.gpg

# Sources update
RUN sudo apt-get update -y

# Misc utils
RUN sudo apt-get install -y git curl wget vim openssh-server apt-utils

# LAMP install
RUN sudo apt-get install -y apache2 php5 php5-cli php5-mysql php5-mcrypt mysql-server

# Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN sudo mv composer.phar /usr/local/bin/composer

# Node.js stuff
RUN curl https://raw.githubusercontent.com/creationix/nvm/v0.7.0/install.sh | sh
RUN echo "[ -s \"//.nvm/nvm.sh\" ] && . \"//.nvm/nvm.sh\"  # This loads nvm" >> /.bash_profile
RUN source /.bash_profile
RUN nvm install 0.10  
RUN nvm alias default 0.10
RUN npm install -g grunt-cli gulp bower

# Webmin install
RUN wget http://prdownloads.sourceforge.net/webadmin/webmin_1.650_all.deb
RUN sudo dpkg -i webmin_1.650_all.deb || true
RUN sudo apt-get install -y -f
RUN rm webmin_*

EXPOSE  80
EXPOSE 3306
EXPOSE 10000
RUN service webmin start
RUN service mysql start
ENTRYPOINT service apache2 start

# How to use this image:
# Once:
# sudo docker build -t rougemine/talk-talk .
# Then, each time you want to use this image :
# sudo docker run -i -t -p 9000:9000 -p 8080:80 -p 3307:3306 -p 10010:10000 -v $PWD:/var/www rougemine/talk-talk /bin/bash
# When you are in the image bash shell:
# service webmin start && service apache2 start && service mysql start
# Commit container changes to its repository:
# sudo docker ps
# sudo docker commit <container_id> rougemine/talk-talk
 
