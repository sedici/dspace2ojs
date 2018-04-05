# dspace2ojs
Import journals from a typical DSpace CSV file into OJS

Sometimes, journals hosted in digital repositories want to start using OJS to manage the editorial process and publish articles
online. The process of migration of articles and issues into OJS by hand may take a long time to add metadata, organize articles, 
upload files, etc.
This tool creates XML import files, compatible with OJS native import schema, from common DSpace-exported csv files. For each item
in the CSV input file, its metadata is parsed into the XML schema and the PDF file is downloaded, base64 encoded and embeded into 
the XML submission record. The URL of the PDF is obtained from the web page of the repository.

# How to use
To use the tool, the user must specify the input csv file, as well as a set of optional params to generate the XML file(s):

php src/bin/csv2xml.php ~/Downloads/Journal/10915-369-JOURNAL into_section=IMPORTED

which would generate an XML file from ~/Downloads/Journal/10915-369-JOURNAL.csv . This file will specifiy as destination section a 
section tagged as IMPORTED.
Running the tool without params will list all available parameters:

php src/bin/csv2xml.php 

              Missing csv file. Example:
      
              php csv2xml.php [filename] [optional params]

              Optional params:
                - authors_group : name used for the author_group_ref (default authors)
                - into_section : section to map imported articles (default IMPORTED)
                - split_csv [yes | no]: if the input CSV file contains items from many collections, items can be splitted into several (smaller) csv files, one per collection
                - limit: maximum number of articles to process (usefull for testing purposes)
      Example:
      php csv2xml.php ~/journal_xyz authors_group=autores into_section=IMPORTADOS split=no
      Will process ~/journal_xyz.csv file and output ~/journal_xyz.xml file . Authors will be mapped into autores group (which must exist in OJS), and articles will be placed into IMPORTADOS section (which must also exist in OJS) 


