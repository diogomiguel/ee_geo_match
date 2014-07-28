# GEO Match

* Author: [Diogo Silva](https://github.com/diogomiguel/ee_geo_match)

## Version 0.0.1

Tested only on Expression Engine 2.7+. Shouldn't raise any compatibility problems on any Expression Engine 2+ version, but use with caution.

* Requires: ExpressionEngine 2.7+

## Description

Simple geo-target implementation for Expression Engine, based on [GeoPlugin](http://www.geoplugin.com/)

## Instalation

Unzip the downloaded zip and place the 'geo_match' folder in /expressionengine/third_party/.

## Documentation

Usage
	
If countries param is empty, it will return the user country code.
	
	{exp:geo_match}

If not it will return 1 (true) if it matches with any of the provided country coded or 0 (false) if it doesn't.
	
	{exp:geo_match countries="gb,fr,de" /}

Countries code list [here](http://www.geoplugin.com/iso3166)