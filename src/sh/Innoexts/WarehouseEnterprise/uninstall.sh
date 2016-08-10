#!/bin/sh
CWD=$(pwd)
rm -fr $CWD/app/code/local/Innoexts/WarehouseEnterprise/
rm -f $CWD/app/design/adminhtml/default/default/layout/warehouseenterprise.xml
rm -fr $CWD/app/design/frontend/enterprise/default/template/warehouse/
rm -f $CWD/app/design/frontend/enterprise/default/layout/warehouseenterprise.xml
rm -fr $CWD/app/design/frontend/enterprise/iphone/template/warehouse/
rm -f $CWD/app/design/frontend/enterprise/iphone/layout/warehouseenterprise.xml
rm -f $CWD/app/etc/modules/Innoexts_WarehouseEnterprise.xml
rm -f $CWD/app/locale/en_US/Innoexts_WarehouseEnterprise.csv
rm -fr $CWD/skin/frontend/enterprise/default/css/customerlocator/
rm -fr $CWD/skin/frontend/enterprise/default/css/warehouse/
rm -fr $CWD/skin/frontend/enterprise/iphone/css/customerlocator/
rm -fr $CWD/skin/frontend/enterprise/iphone/css/warehouse/
rm -fr $CWD/sql/Innoexts/WarehouseEnterprise/
rm -f $CWD/var/connect/Innoexts_WarehouseEnterprise.xml
