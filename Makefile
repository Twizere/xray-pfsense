PORTNAME=       pfSense-pkg-Xray
PORTVERSION=    0.1
CATEGORIES=     net

MAINTAINER=     your_email@example.com
COMMENT=        Xray secure tunneling package for pfSense

LICENSE=        BSD2CLAUSE

# Set default values for directories
PREFIX?=/usr/local
DESTDIR?=
WRKSRC=.
MKDIR=mkdir -p

# Extract phase (not used in BSD ports without bsd.port.mk)
do-extract:
	@echo "Extracting package files..."
	${MKDIR} ${WRKSRC}

# Install phase
do-install:
	@echo "Installing Xray package..."

	# Create necessary directories
	${MKDIR} ${DESTDIR}${PREFIX}/bin
	${MKDIR} ${DESTDIR}${PREFIX}/etc/xray
	${MKDIR} ${DESTDIR}${PREFIX}/etc/inc/priv
	${MKDIR} ${DESTDIR}${PREFIX}/pkg
	${MKDIR} ${DESTDIR}${PREFIX}/www/packages/xray
    ${MKDIR} ${DESTDIR}${PREFIX}/www/widgets/widgets

	# Install xray binary (make sure it's executable)
	install -m 755 ${WRKSRC}/bin/xray ${DESTDIR}${PREFIX}/bin/

	# Install configuration files
	install -m 644 ${WRKSRC}/etc/xray/config.json ${DESTDIR}${PREFIX}/etc/xray/
	install -m 644 ${WRKSRC}/etc/inc/priv/xray.priv.inc ${DESTDIR}${PREFIX}/etc/inc/priv/
	install -m 644 ${WRKSRC}/usr/local/pkg/xray.inc ${DESTDIR}${PREFIX}/usr/local/pkg/
	install -m 644 ${WRKSRC}/usr/local/www/packages/xray/index.php ${DESTDIR}${PREFIX}/usr/local/www/packages/xray/
    install -m 644 ${WRKSRC}/usr/local/www/widgets/xray.widget.php ${DESTDIR}${PREFIX}/usr/local/www/widgets/widgets/
# Clean up (optional)
do-clean:
	@echo "Cleaning up..."
#	rm -rf ${WRKSRC}

