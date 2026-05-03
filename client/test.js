import crypto from "crypto";
import fs from "fs";
import axios from "axios";

export const timeStamp = () => Math.floor(Date.now() / 1000)

const encrypt = (text) => {
    const key = 'add your solt';
    const algorithm = 'aes-256-cbc';
    const iv = crypto.randomBytes(16);
    const cipher = crypto.createCipheriv(algorithm, key, iv);

    const encryptedValue = cipher.update(text, 'utf8', 'hex') + cipher.final('hex');
    return {encryptedValue, iv: iv.toString('hex')};
};


async function sendRequestToServer() {

    const encryptedToken = encrypt(timeStamp()+'');

    let data = {
        yearMonth : '140412',
        token:encryptedToken,
    }
    const config = {
        url : 'https://__your Host__/frontend/rfm/exportirx4fghghjgh6h87eriu',
        data,
        method: 'post',
        headers: {"Content-Type": 'multipart/form-data','Connection': 'close'},
        responseType: 'arraybuffer',
    }
    try {
        const response = await axios(config);

        let fileName = `rfm_data_${data.yearMonth}.csv`;
        const contentDisposition = response.headers['content-disposition'];
        if (contentDisposition) {
            const match = contentDisposition.match(/filename="?([^"]+)"?/);
            if (match && match[1]) {
                fileName = match[1];
            }
        }

        fs.writeFileSync(fileName, Buffer.from(response.data));
        console.log('فایل با موفقیت دانلود و ذخیره شد.');
    } catch (error) {
        if (error.response) {
            console.error('Error Status:', error.response.status);
            console.error('Error Data:', error.response.data);
        } else {
            console.error('Error:', error.message);
        }
    }

}


sendRequestToServer()
