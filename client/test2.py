import os
import time
import re
import requests
from Crypto.Cipher import AES
from Crypto.Util.Padding import pad

def encrypt(text):
    key = b'add your solt'
    iv = os.urandom(16)
    cipher = AES.new(key, AES.MODE_CBC, iv)
    padded_text = pad(text.encode('utf-8'), AES.block_size)
    
    encrypted_value = cipher.encrypt(padded_text)
    
    return {
        'encryptedValue': encrypted_value.hex(),
        'iv': iv.hex()
    }

def get_timestamp():
    return str(int(time.time()))

def send_request_to_server():
    try:
        encrypted_token = encrypt(get_timestamp())
        payload = {
            'yearMonth': '140410',
            'token[encryptedValue]': encrypted_token['encryptedValue'],
            'token[iv]': encrypted_token['iv']
        }

        url = 'https://__your Host__/frontend/rfm/exportirx4fghghjgh6h87eriu'
        
        print("⏳ در حال ارسال درخواست...")
        multipart_data = {key: (None, str(value)) for key, value in payload.items()}

        response = requests.post(url, files=multipart_data)
        response.raise_for_status()

        filename = 'rfm_data.csv' 
        content_disposition = response.headers.get('Content-Disposition')
        
        if content_disposition:
            matches = re.findall(r'filename="?([^"]+)"?', content_disposition)
            if matches:
                filename = matches[0]

        with open(filename, 'wb') as f:
            f.write(response.content)
            
        print(f'✅ فایل با موفقیت با نام "{filename}" ذخیره شد')

    except requests.exceptions.HTTPError as e:
        print('❌ خطای HTTP:', e)
        if e.response is not None:
            print('📄 Status:', e.response.status_code)
            print('📄 Data:', e.response.text)
            
    except requests.exceptions.RequestException as e:
        print('📡 خطای شبکه رخ داد یا سرور در دسترس نیست.')
        print('جزئیات:', e)

if __name__ == '__main__':
    send_request_to_server()
