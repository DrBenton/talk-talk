# DOCKER-VERSION 0.9
FROM    ubuntu:12.04
MAINTAINER Olivier Philippon <olivier@rougemine.com>

# let's install "whois" first, for its "mkpasswd" tool
RUN apt-get install -y whois

ENV USER_LOGIN dev-user
RUN useradd -m -p `mkpasswd docker` $USER_LOGIN
RUN chsh -s /bin/bash $USER_LOGIN
RUN su - $USER_LOGIN -c "touch /home/$USER_LOGIN/.bashrc"

ENV DEBIAN_FRONTEND noninteractive

# Sources update
RUN apt-get update -y

# Ubuntu PPA activation
RUN apt-get install -y python-software-properties

# Misc utils
RUN apt-get install -y git curl wget vim openssh-server apt-utils sudo

# Our dev user is a sudoer
RUN usermod -a -G sudo $USER_LOGIN

# LAMP install
RUN apt-get install -y apache2 mysql-server php5 php5-cli php5-mysql php5-mcrypt php5-xdebug

# Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

# App VirtualHost setup
RUN sh -c 'echo "<VirtualHost *:80> \n \
DocumentRoot "/host/" \n  \
ServerAlias dev.talk-talk.com \n \
EnableSendfile Off \n \
EnableMMAP Off \n \
<Directory "/host/"> \n \
AllowOverride All \n \
allow from all \n \
Options +Indexes \n \
</Directory> \n \
</VirtualHost>\n \
" >> /etc/apache2/sites-available/app-dev'
RUN a2ensite app-dev
# Apache default VHost is disabled
RUN a2dissite default
# App modules activation
RUN a2enmod rewrite

# PHP XDebug setup
RUN sh -c 'echo "xdebug.remote_enable=1 \n \
xdebug.remote_autostart=0 \n \
xdebug.remote_connect_back=1 \n \
xdebug.remote_port=9000 \n \
" >> /etc/php5/conf.d/xdebug.ini'

ENV BASHRC "/home/$USER_LOGIN/.bashrc"

# Node.js, Grunt, Gulp & Bower
RUN su - $USER_LOGIN -c "git clone https://github.com/creationix/nvm.git /home/$USER_LOGIN/.nvm"
RUN su - $USER_LOGIN -c "echo '[ -s /home/$USER_LOGIN/.nvm/nvm.sh ] && . /home/$USER_LOGIN/.nvm/nvm.sh # This loads NVM' >> $BASHRC"
RUN su - $USER_LOGIN -c "source /home/$USER_LOGIN/.nvm/nvm.sh && nvm install 0.10"
RUN su - $USER_LOGIN -c "source /home/$USER_LOGIN/.nvm/nvm.sh && nvm alias default 0.10"
RUN su - $USER_LOGIN -c "source /home/$USER_LOGIN/.nvm/nvm.sh && npm install -g grunt-cli gulp bower less"

# Some shell stuff for our "dev" user
ENV QUOTE "'"
RUN su - $USER_LOGIN -c "echo 'force_color_prompt=yes' >> $BASHRC"

RUN su - $USER_LOGIN -c "echo 'alias ls=${QUOTE}ls --color=auto${QUOTE}' >> $BASHRC"
RUN su - $USER_LOGIN -c "echo 'alias ll=${QUOTE}ls -Al${QUOTE}' >> $BASHRC"
RUN su - $USER_LOGIN -c "echo 'alias start=${QUOTE}sudo service apache2 start && sudo service mysql start && sudo service webmin start${QUOTE}' >> $BASHRC"
RUN su - $USER_LOGIN -c "echo 'alias stop=${QUOTE}sudo service apache2 stop && sudo service mysql stop && sudo service webmin stop${QUOTE}' >> $BASHRC"
RUN su - $USER_LOGIN -c "echo 'alias restart=${QUOTE}sudo service apache2 restart && sudo service mysql restart && sudo service webmin restart${QUOTE}' >> $BASHRC"

# Webmin install
RUN wget -nv http://prdownloads.sourceforge.net/webadmin/webmin_1.650_all.deb
RUN dpkg -i webmin_1.650_all.deb || true
RUN apt-get install -y -f
RUN rm webmin_*

EXPOSE  80
EXPOSE 3306
EXPOSE 10000

# Start our services, then log as the dev user
ENTRYPOINT `/usr/sbin/mysqld >/dev/null 2>&1 &` && service apache2 start && service webmin start && su - $USER_LOGIN

# How to use this image:
# Once:
# docker build -t rougemine/talk-talk-ubuntu .
# Then, each time you want to use this image :
# docker run -i -t -p 9000:9000 -p 8080:80 -p 3307:3306 -p 10010:10000 -v $PWD:/host rougemine/talk-talk-ubuntu /bin/bash
# Commit container changes to its repository:
# docker ps
# docker commit <container_id> rougemine/talk-talk-ubuntu
 
