# 构建Ai-To-PPTX项目
# 把前端项目编译为静态文件, 目录为: /var/www/html , 同时在前端中把后端地址修改为 /aipptx/
# 把后端项目的PHP文件放到 /var/www/html/aipptx 目录
# 安装Redis服务器端和PHP的Redis扩展
# /var/www/html/aipptx/cache 和 /var/www/html/aipptx/output 两个目录要求可写


# 使用官方的 PHP 8.2 镜像，并包含 Apache
FROM php:8.2-apache

# 安装所需的 PHP 扩展
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libcurl4-openssl-dev \
    unzip \
    redis \
    && docker-php-ext-install zip curl pdo_mysql

# 安装 PHP 的 Redis 扩展
RUN pecl install redis && docker-php-ext-enable redis

# 启用 Apache 的 rewrite 模块
RUN a2enmod rewrite

# 设置工作目录
RUN mkdir -p /var/www/html/aipptx
WORKDIR /var/www/html/aipptx

# 复制代码到容器中
COPY . .

RUN mkdir -p /var/www/html/aipptx/cache && \
    chown -R www-data:www-data /var/www/html/aipptx/cache && \
    chmod -R 775 /var/www/html/aipptx/cache

RUN mkdir -p /var/www/html/aipptx/output && \
    chown -R www-data:www-data /var/www/html/aipptx/output && \
    chmod -R 775 /var/www/html/aipptx/output


# 安装 git Node.js 和 npm, 主要用于安装和编译前端项目
RUN apt-get update && apt-get install -y git nodejs npm

# 克隆 ai-to-pptx 项目到 /var/www/html/ai-to-pptx
RUN mkdir -p /var/www/html/ai-to-pptx
RUN git clone https://github.com/chatbookai/ai-to-pptx.git /var/www/html/ai-to-pptx

# 修改 Config.ts 文件中的 BackendApi 值
# 源代码中是后端的演示地址, 需要在前端中修改为DOCKER中本地镜像中的地址.
# 因为前端项目编译为静态的HTML和CSS文件以后,和后端的项目是在同一个Webroot下面,所以路径只需要写为 /aipptx/ , 如果你的后端是一个独立的URL地址, 则需要写完整的地址.

RUN sed -i 's|export const BackendApi = .*|export const BackendApi = "/aipptx/";|' /var/www/html/ai-to-pptx/src/views/AiPPTX/Config.ts

# 安装 ai-to-pptx 项目的依赖
WORKDIR /var/www/html/ai-to-pptx
RUN npm install
RUN npm run build
RUN mv /var/www/html/ai-to-pptx/webroot/* /var/www/html

# 暴露端口 80（Apache 默认端口）和 6379（Redis 默认端口）
EXPOSE 80

# 启动 Apache 和 Redis
CMD ["sh", "-c", "redis-server --daemonize yes && apache2-foreground"]
