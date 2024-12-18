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
install:
	@echo "Installing Xray package..."

	# Create necessary directories
	${MKDIR} ${DEST}/bin
	${MKDIR} ${DEST}/etc/xray
	${MKDIR} ${DEST}/etc/inc/priv
	${MKDIR} ${DEST}/pkg
	${MKDIR} ${DEST}/www/packages/xray
	${MKDIR} ${DEST}/www/widgets/widgets


	# Install xray binary (make sure it's executable)
	install -m 755 ${WRKSRC}${DEST}/bin/xray ${DEST}/bin/

    # Install xray serbice (make sure it's executable)
	install -m 755 ${WRKSRC}/etc/rc.d/xray ${DEST}/etc/rc.d/

	# Install configuration files
	install -m 644 ${WRKSRC}/etc/xray/config.json ${DEST}/etc/xray/
	install -m 644 ${WRKSRC}/etc/inc/priv/xray.priv.inc ${DEST}/etc/inc/priv/
	install -m 644 ${WRKSRC}${DEST}/pkg/xray.inc ${DEST}/pkg/
	install -m 644 ${WRKSRC}${DEST}/www/packages/xray/index.php ${DEST}/www/packages/xray/
	install -m 644 ${WRKSRC}${DEST}/www/widgets/widgets/xray.widget.php ${DEST}/www/widgets/widgets/
    install -m 644 ${WRKSRC}${DEST}/www/widgets/widgets/xray-cert.widget.php ${DEST}/www/widgets/widgets/
    # Installing the service
	@echo "Installing the service..."
	chmod +x ${DEST}/etc/rc.d/xray
	sysrc xray_enable="YES"

     # Installing the service
	@echo "Running the service"
    # Check if Xray is running before starting the service
	if ! pgrep -f "/usr/local/bin/xray" > /dev/null; then \
		service xray start; \
	else \
		echo -e "\033[0;32mXray is already running.\033[0m"; \
	fi


# Clean up (optional)
do-clean:
	@echo "Cleaning up..."
#	rm -rf ${WRKSRC}
