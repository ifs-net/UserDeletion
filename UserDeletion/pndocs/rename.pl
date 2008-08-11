#!/usr/bin/perl

# Author Stephen Williams
# Contact sunadmn@yahoo.com

use strict;
#use warnings;
use File::Find;



# We need to gather some info from the user
# here for module name

print "Please enter your module name\n";
print "This would be the name of the directory of your module\n";
       

my $mname   = <STDIN>;
chomp($mname);

# Now we need to know where to look to make 
# the changes to files on the system this
# will be our base directory to search through

print "Please enter your modules base directory\n";
print "UserDeletion would be /var/www/postnuke/modules/YOUR_MODULE/\n";

my $base    = <STDIN>;
chomp($base);

my $old_name  = 'userdeletion';


find(\&wanted, $base);


sub wanted {
    if ((-f $File::Find::name) && ($File::Find::name !~ m/\.bak/)) {
        my $old_file_name  = $File::Find::name;


	open(IN, "$old_file_name")   || die "Can't open $old_file_name to read\nReason: $!\n";
	chomp(my @inline = <IN>);
	close(IN);

	(my $new_file_name = $old_file_name) =~ s/$old_name/$mname/;

	open(OUT, ">$new_file_name") || die "Can't create $new_file_name\nReason:$!\n";

	foreach my $line (@inline) {
	   (my $new_line = $line) =~ s/$old_name/$mname/ig;
	   printf OUT "$new_line\n";
	}

	close(OUT);
	unlink $File::Find::name if($File::Find::name =~ /userdeletion/i);
    }
}
