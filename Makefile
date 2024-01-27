.PHONY: up down logs ps clear restart

# 可以通过 make logs n=30 查看最近 30 行日志
n=20

up:
	git fetch --all
	git reset --hard
	git pull
	docker compose pull aws-waf-solver
	docker compose up --build -d
	docker system prune -af

down:
	docker compose down

logs:
	docker compose logs -tf --tail $(n)

ps:
	docker compose ps

clear:
	docker system prune -af
	docker volume prune -af

restart:
	docker compose restart
