while (<STDIN>) {
	chomp;
	@Info = split(' +');
	next if /^Listener/;
	next if /^Instance/;
	next if /^---/;
	next if /^ /;
	$listener = $instance = '';
	if ($#Info>1) {
		($listener,$instance) = @Info;
	}
	else {
		($instance) = @Info;
	}
	$new = "$listener.$instance";
	print "$instance	$listener\n"
		unless $new eq $last;
	$last = $new;
}