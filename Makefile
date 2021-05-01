.PHONY: start-mercure help cs-fixer php-stan lintjs install-mercure run-supervisor server update push

.DEFAULT_GOAL=help

COM_COLOR   = \033[0;34m
OBJ_COLOR   = \033[0;36m
OK_COLOR    = \033[0;32m
ERROR_COLOR = \033[0;31m
WARN_COLOR  = \033[0;33m
NO_COLOR    = \033[m

CURRENT_DIR=$(shell pwd)

DIR=$(CURRENT_DIR)/mercure_binary

##
help: ## Help
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-10s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
##
start-mercure: ## Start mercure server on port 30 
	echo  "mercure server started on $(OK_COLOR)http://localhost:3000$(NO_COLOR)"
	$(DIR)/mercure --jwt-key='!ChangeMe!' --debug=1 --addr='localhost:3000' --allow-anonymous --cors-allowed-origins='http://localhost:8000'

##
cs-fixer: ## Run php-cs-fixer command to Check if the my php is correct
	echo  "Runig $(WARN_COLOR)./php-cs-fixer --diff --dry-run -v --allow-risky=yes fix$(NO_COLOR)"
	./php-cs-fixer --diff --dry-run -v --allow-risky=yes fix

##
php-stan: ## Run the php-stan command : vendor/bin/phpstan analyse src --level 5
	echo  "Run the php-stan command: $(COM_COLOR)vendor/bin/phpstan analyse src --level 5$(NO_COLOR)"
	vendor/bin/phpstan analyse src --level 5

##
lintjs: ## Run eslint command : yarn run eslint assets/js/pages/
	echo  "Run eslint command : $(OBJ_COLOR)yarn run eslint assets/js/pages/$(NO_COLOR)"
	yarn run eslint assets/js/pages/ --fix
##
##Download Mercure Hub And Extract It in mercure_binary folder
install-mercure: ##  make install-mercure DIR="/path/when/mercure/willbe/installed"
	echo "Creating $(COM_COLOR) $(DIR) $(NO_COLOR) Folder"
	rm -rf $(DIR)
	mkdir $(DIR)
	echo "$(OK_COLOR)Downloading..$(NO_COLOR)"
	wget https://github.com/dunglas/mercure/releases/download/v0.10.4/mercure_0.10.4_Linux_x86_64.tar.gz -P $(DIR)
	echo "$(OK_COLOR) Extracting the downloads$(NO_COLOR)"
	cd $(DIR) && tar -xvzf mercure_0.10.4_Linux_x86_64.tar.gz

##
##Install Supervisor
run-supervisor: ## take configuration from 'config' folder and run supervisor
	sudo apt-get update
	sudo apt-get install supervisor 
	echo "$(OBJ_COLOR)create symlink to the configuration$(NO_COLOR)"
	sudo ln -s ./config/messenger-worker.conf /etc/supervisor/conf.d
	sudo supervisorctl reread
	sudo supervisorctl update
	sudo supervisor start messenger-consume:*
##

##
##Installation
install: ## What I have to install to run the project
	sudo apt-get install php7.4-gd php-mysql php7.4-xml php7.4-mbstring php-redis php7.4-intl
	sudo apt-get install -y jpegoptim
	sudo apt-get install wkhtmltopdf

##
##Run the worker
worker: ## php bin/console messenger:consume async -vvv 
	php bin/console messenger:consume async -vvv

##
##Update Project
update: ## Update the projects dependences
	composer update
	yarn install

##
##Update Server
deploy: ## Deploy fast in the beta server
	git pull
	composer update
	php bin/console doctrine:schema:update -f
	yarn install
	yarn run encore production