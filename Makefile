PORTNAME=        pfSense-pkg-Xray
PORTVERSION=     0.1
CATEGORIES=      sysutils
MAINTAINER=      your-email@example.com
COMMENT=         Xray package for pfSense
LICENSE=         BSD2CLAUSE

USE_RC_SUBR=     xray
RUN_DEPENDS=     xray:/usr/local/bin/xray

NO_BUILD=        yes
NO_ARCH=         yes

PLIST_FILES=     /usr/local/bin/xray

do-install:
    ${MKDIR} ${STAGEDIR}/usr/local/bin/
    ${INSTALL_SCRIPT} ${WRKSRC}/files/usr/local/bin/xray ${STAGEDIR}/usr/local/bin/xray
