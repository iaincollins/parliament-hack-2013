parliament-hack-2013
===================

UK Parliament Hack 2013 #RSPARLY2013
http://rewiredstate.org/hacks/uk-parliament-hack-2013

Documentation for relevant APIs can be found here:
https://s3.amazonaws.com/ParliamentHack2013/Parliament+Hack+2013.htm

ABOUT THIS HACK

Saturday 11th, 10:45:

While on the tube the way to the pre-hack drinks last night, I wrote down a
few ideas on my iPhone.

This is my favourite and what I think I'll use as my brief for the weekend.

"A system for public review and critique of new legislation, allowing line by
line annotation."

- Allow for ranked feedback and encouraging participation and public
  commentary.
- Summarise feedback for MPs, journalists and other interested parties and
  allow replies to annotations.
- Automatically find related commentary by members by looking up Hansard.
- Automatically tag bills and allow people can subscribe to receive email
  alerts for legislation matching their interests.
- Show the state of the bill and how members have voted on it as time passes.

I'm not certain if the API's will allow this yet, but it looks like they at
least /almost/ do and that hackery is possible where it doesn't.

It may be necessary to screen scrape http://services.parliament.uk/bills/ to
get things like the text of bills (and convert them from PDF to plain text
or HTML).

There is an RSS feed for bills:
http://services.parliament.uk/bills/AllBills.rss

There is also an XML dump of the data here:
https://s3.amazonaws.com/ParliamentHack2013/Bills.zip

Drafts of public bills are posted in a PDF here:
http://www.parliament.uk/business/bills-and-legislation/current-bills/public-bill-list/

"The Progress of Public Bills list includes the dates of all stages of public
bills laid before Parliament this session. The list is published every 
Friday during sitting time by midday."

This would need parsing of PDF's to identify bills by name and member.

Hansard API's are here:
http://hansard.millbanksystems.com/api

It isn't currently possible to search Hansard by keyword may fall back to 
showing related data from other sources (BBC, Guardian, Google, etc - ideally
a broad range) for relevant commentary. You can look up by member though.

Additional API for looking up members:
http://data.parliament.uk/membersdataplatform/memberquery.aspx

Also possible to screen scrape for members using their PIMS ID in these URLs:
http://www.publications.parliament.uk/pa/cm201314/cmhansrd/cmallfiles/mps/commons_hansard_3572_home.html
http://data.parliament.uk/membersdataplatform/services/mnis/members/query/name*Abbot/

NB: For lords the URL format for member pages is:
http://www.publications.parliament.uk/pa/ld201314/ldhansrd/ldallfiles/peers/lord_hansard_5355_od.html

Thanks to @stephen_abbott for explaining a bit about the API's and walking me
through the significance of some of the values!