import os
import requests
from dotenv import load_dotenv

load_dotenv()

API_KEY = os.getenv("OPENROUTER_API_KEY")


def ask_gemma(prompt: str):

    # Validasi API Key
    if not API_KEY:
        return {
            "success": False,
            "error": "OPENROUTER_API_KEY tidak ditemukan di file .env"
        }

    url = "https://openrouter.ai/api/v1/chat/completions"

    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json"
    }

    data = {
        "model": "google/gemma-4-31b-it",
        "messages": [
            {
                "role": "user",
                "content": prompt
            }
        ]
    }

    try:

        response = requests.post(
            url,
            headers=headers,
            json=data,
            timeout=60
        )

        # Jika response gagal
        response.raise_for_status()

        result = response.json()

        # Ambil isi jawaban AI
        answer = result["choices"][0]["message"]["content"]

        # Ambil metadata usage
        usage = result.get("usage", {})

        return {
            "success": True,
            "model": result.get("model"),
            "response": answer,
            "usage": {
                "prompt_tokens": usage.get("prompt_tokens"),
                "completion_tokens": usage.get("completion_tokens"),
                "total_tokens": usage.get("total_tokens"),
                "cost": usage.get("cost")
            }
        }

    except requests.exceptions.Timeout:
        return {
            "success": False,
            "error": "Request timeout ke OpenRouter"
        }

    except requests.exceptions.HTTPError as e:
        return {
            "success": False,
            "error": f"HTTP Error: {str(e)}",
            "details": response.text
        }

    except requests.exceptions.RequestException as e:
        return {
            "success": False,
            "error": f"Request Error: {str(e)}"
        }

    except KeyError:
        return {
            "success": False,
            "error": "Format response OpenRouter tidak sesuai",
            "raw_response": result
        }

    except Exception as e:
        return {
            "success": False,
            "error": f"Unexpected Error: {str(e)}"
        }