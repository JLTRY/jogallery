VERSION = "3.0.0"
VERSION2 = $(shell echo $(VERSION)|sed 's/ /-/g')
PACKAGE = pkg_jogallery
ZIPFILE = $(PACKAGE)-$(VERSION2).zip
UPDATEFILE = update_pkg.xml
ROOT = $(shell pwd)
mkfile_path := $(abspath $(lastword $(MAKEFILE_LIST)))
mkfile_dir := $(dir $(mkfile_path))


all: parts $(ZIPFILE) fixsha

INSTALLS = com_jogallery jogallery

EXTRAS = 

NAMES = $(INSTALLS) $(EXTRAS)

ZIPS = $(NAMES:=.zip)

ZIPIGNORES = -x "*.git*"

parts: $(ZIPS)

%.zip:
	@echo "-------------------------------------------------------"
	@echo "Creating zip file for: $*"
	@rm -f $@
	@(cd $*; zip -r ../$@ * $(ZIPIGNORES))

$(ZIPFILE): $(ZIPS)
	@echo "-------------------------------------------------------"
	@echo "Creating extension zip file: $(ZIPFILE)"
	@mv $(INSTALLS:=.zip) $(PACKAGE)/packages/
	@(cd $(PACKAGE); zip -r ../$@ * $(ZIPIGNORES))
	@echo "-------------------------------------------------------"
	@echo "Finished creating package $(ZIPFILE)."



fixversions:
	@echo "Updating all install xml files to version $(VERSION)"
	@export ATVERS=$(VERSION); export ATDATE=$(DATE); find . \( -name '*.xml' ! -name 'default.xml' ! -name 'metadata.xml' ! -name 'config.xml' \) -exec  ./fixvd {} \;
revertversions:
	@echo "Reverting all install xml files"
	@find . \( -name '*.xml' ! -name 'default.xml' ! -name 'metadata.xml' ! -name 'config.xml' \) -exec git checkout {} \;

fixsha:
	@echo "Updating update xml files with checksums"
	@(cd $(ROOT);./fixsha.sh $(ZIPFILE) $(UPDATEFILE))
untabify:
	@find . -name '*.php' -exec $(mkfile_dir)/replacetabs.sh {} \;
