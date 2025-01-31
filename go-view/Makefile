如何安装,常规命令会报错,需要执行以下命令:

git clone https://gitee.com/dandiankeji/go-view -b master-fetch --depth 1


.PHONY: dist test
default: help

install:
	npm install -g increase-memory-limit
	increase-memory-limit
	
dev:
	npm run dev

dist:
	需要先执行以下命令: set NODE_OPTIONS=--max-old-space-size=4096
	npm run build

view:
	npm run preview

lint:
	npm run lint

new:
	npm run new


	
help:
	@echo "    make dev [npm run dev] 开发模式"
	@echo "    make dist [npm run build] 编译模式"
	@echo "    make view [npm run preview] 预览打包文件"
	@echo "    make new [npm run lint] 通过自动化流程创建代码"
	@echo "    make lint [npm run new] 格式校验"