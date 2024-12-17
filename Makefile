PORTNAME=       pfSense-pkg-Xray
PORTVERSION=    0.1
CATEGORIES=     net
MASTER_SITES=   # Leave empty if no external sources are needed

MAINTAINER=     your_email@example.com
COMMENT=        Xray secure tunneling package for pfSense

LICENSE=        BSD2CLAUSE

USES=           pkgconfig
NO_BUILD=       yes

PLIST_FILES=    bin/xray \
                etc/xray/config.json \
                etc/inc/priv/xray.priv.inc \
                usr/local/pkg/xray.inc \
                usr/local/www/packages/xray/index.php
do-extract:
#	 ${MKDIR} ${WRKSRC}

do-install:
#     ${MKDIR} ${WRKSRC}/etc/inc/priv
#     ${INSTALL_SCRIPT} ${WRKSRC}/bin/xray ${STAGEDIR}${PREFIX}/bin/
#     ${INSTALL_DATA} ${WRKSRC}/etc/xray/config.json ${STAGEDIR}${PREFIX}/etc/xray/
#     ${INSTALL_DATA} ${WRKSRC}/etc/inc/priv/xray.priv.inc ${STAGEDIR}${PREFIX}/etc/inc/priv/
#     ${INSTALL_DATA} ${WRKSRC}/usr/local/pkg/xray.inc ${STAGEDIR}${PREFIX}/usr/local/pkg/
#     ${INSTALL_DATA} ${WRKSRC}/usr/local/www/packages/xray/index.php ${STAGEDIR}${PREFIX}/usr/local/www/packages/xray/

# .include <bsd.port.mk>


