# Datalayer Generator


MessageGenerator - generuje zpravy/objekty pro dataLayer, tj. to co se posila pomoci dataLayer.push()
pro ruzne akce (detail, click, add, checkout ...)
 - CheckoutGenerator
 - ImpressionsGenerator
 - ProductDetailGenerator
 - PromotionGenerator
 - PurchaseGenerator

https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
EcDatatype - generuje struktury pro zakladni datatypy:
 - Impression - informace spojene se zobrazeni, produktu
 - Product - informace o produktu spojene s ecommerce aktivitami (detail, add, checkout ...)
 - Promotion - bannery
 - jeste chybi ActionData - informace spojene s ecommerce
