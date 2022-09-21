Hướng dẫn cài đặt trên window:
    - Yêu cầu Node v12.21.0 , npm 6.14.11 composer 1.10.12 , Xampp phiên bản 7.4.13
    - Giải nén thứ mục cofigStorage 
    - Repalce storage vừa giải nén vào thư mục SourceCode/pfmis/
    - Tại SourceCode/BackEnd/pfmis chạy lệnh: composer install
    - Coopy thư mục ijsmartbooks vào SourceCode/BackEnd/pfmis/vendor
    - Cấu hình tên miền ảo: 
        + Tại C:\Windows\system32\drivers\etc\  file host thêm :
                127.0.0.1 			dev.pfmis.com
                127.0.0.1           localhost
        + Tại xampp\apache\conf\extra\ file httpd-vhosts.conf trỏ về thư mục pfmis/public: 
            Ví dụ
                <VirtualHost *:80>
                ServerAdmin dev.pfmis.com
                DocumentRoot "D:/smartbooks/pfmis/SourceCode/BackEnd/pfmis/public"
                ServerName dev.pfmis.com
                ErrorLog "logs/dummy-host2.example.com-error.log"
                CustomLog "logs/dummy-host2.example.com-access.log" common
                <Directory "D:/smartbooks/pfmis/SourceCode/BackEnd/pfmis/public">
                Options Indexes FollowSymLinks MultiViews
                AllowOverride all
                Order Deny,Allow
                Allow from all
                Require all granted
                    </Directory>
                </VirtualHost>
Tại thư mục SourceCode/Backend/pfmis copy file env.example thành .env 
Tại SourceCode/BackEnd/pfmis chạy lệnh: composer dump-autoload
Tại thư mục SourceCode\FrontEnd\Web\vuejs copy file env.example thành .env
Tại SourceCode\FrontEnd\Web\vuejs chạy lệnh: npm run install
Tại SourceCode\FrontEnd\Web\vuejs chạy lệnh: npm run serve
