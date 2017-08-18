# anonymizer

A plugin that removes identifying properties from uploaded submissions in OJS

# About


This plugin for OJS removes identifying properties from submission
uploads based on file type. Note that it does not remove identifying data from
the *contents* of a submission, but only from its file metadata. The user is expected to
anonymize the contents.

# Requirements

OJS 3.0

# Installation

Copy the plugin contents to plugins/generic folder.

# Configuration

1. Enable the plugin from Settings -> Website -> Plugins

2. Install the command line tool you'd like to use to remove metadata from a document.

In the OJS config.inc.php, under the section ~[anon]~, add the command line invocation
for the desired file type, where file type can be **default**, **excel**, **html**, **image**,
**pdf**, **word**, **epub** or **zip**.

For example, for a PDF, we can use `exiftool` and `qpdf` to remove metadata. We'd assign the following
command:

``` text
[anon]
anon[pdf] = "exiftool -all:all= '$fileName' && qpdf --linearize '$fileName' '$fileName_temp' && mv '$fileName_temp' '$fileName' && rm '$fileName_original'"
```

`$fileName` will automatically substitute the uploaded file into the command line invocation.
