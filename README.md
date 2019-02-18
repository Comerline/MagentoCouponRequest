# Magento Coupon Request

Magento 2 Module. Module that allows users of our store to request discount coupons from the product page.

## Installing

- Simply download or clone this repository
- Copy content to root install Magento 2 in **app/code/Comerline/Comerline_CouponRequest**
- Execute **bin/magento module:enable Comerline_Comerline_CouponRequest**
- Execute **bin/magento setup:upgrade**
- Execute **bin/magento setup:di:compile**
- Execute **bin/magento cache:clean**
- Execute **bin/magento cache:flush**

## Use

Access the Admin >> Stores >> Configuration >> Comerline >> Coupon Request and configure the categories you want to include, the categories you want to exclude, the email from and the email to.

## Authors

* **Alejandro Lucena Archilla** - [www.comerline.es](http://www.comerline.es/)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
