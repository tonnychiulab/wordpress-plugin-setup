import sys
import os
import argparse

# 1. 確保能找到 MemoryOS 核心 (指向你的實際路徑)
MEMORY_PATH = r"D:\wordpress-plugin-setup\MemoryOS\memoryos-pypi"
sys.path.append(MEMORY_PATH)

# 2. 注入我們剛才測試成功的 API Key
os.environ["OPENAI_API_KEY"] = "sk-proj-txs0...你的金鑰...zQA"

from memoryos import Memoryos

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--action", choices=["save", "query"])
    parser.add_argument("--text", type=str)
    args = parser.parse_args()

    # 初始化大腦
    memo = Memoryos(
        user_id="tonny_chief",
        assistant_id="security_brain",
        openai_api_key=os.environ["OPENAI_API_KEY"],
        embedding_model_name="BAAI/bge-m3"
    )

    if args.action == "save":
        memo.add_memory(user_input=args.text, agent_response="✅ 記憶已存入資安庫。")
        print(f"✅ 記憶已存入：{args.text[:30]}...")
    
    elif args.action == "query":
        # 這裡會觸發剛才跑通的檢索邏輯
        response = memo.get_response(args.text)
        print(f"🔍 記憶檢索結果：{response}")

if __name__ == "__main__":
    main()