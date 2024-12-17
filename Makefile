PORTNAME=       pfSense-pkg-Xray
PORTVERSION=    0.1
CATEGORIES=     net

MAINTAINER=     your_email@example.com
COMMENT=        Xray secure tunneling package for pfSense

LICENSE=        BSD2CLAUSE

# Set default values for directories
DEST?=/usr/local
WRKSRC=./files
MKDIR=mkdir -p

# Extract phase (not used in BSD ports without bsd.port.mk)
do-extract:
	@echo "Extracting package files..."
	${MKDIR} ${WRKSRC}

# Install phase
do-install:
	@echo "Installing Xray package..."

	# Create necessary directories
	${MKDIR} ${DEST}/bin
	${MKDIR} ${DEST}/etc/xray
	${MKDIR} ${DEST}/etc/inc/priv
	${MKDIR} ${DEST}/pkg
	${MKDIR} ${DEST}/www/packages/xray
	${MKDIR} ${DEST}/www/widgets/widgets

	# Install xray binary (make sure it's executable)
	install -m 755 ${WRKSRC}/bin/xray ${DEST}/bin/

	# # Install configuration files
	# install -m 644 ${WRKSRC}/etc/xray/config.json ${DEST}/etc/xray/
	# install -m 644 ${WRKSRC}/etc/inc/priv/xray.priv.inc ${DEST}/etc/inc/priv/
	# install -m 644 ${WRKSRC}/usr/local/pkg/xray.inc ${DEST}/usr/local/pkg/
	# install -m 644 ${WRKSRC}/usr/local/www/packages/xray/index.php ${DEST}/usr/local/www/packages/xray/
	# install -m 644 ${WRKSRC}/usr/local/www/widgets/xray.widget.php ${DEST}/usr/local/www/widgets/widgets/

# Clean up (optional)
do-clean:
	@echo "Cleaning up..."
#	rm -rf ${WRKSRC}
