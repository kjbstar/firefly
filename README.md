# Firefly
Firefly is a PHP based money manager. It's built to support my own finances, but
can be used by pretty much anybody.

## Introduction
Firefly is pretty simple. First and foremost, Firefly can be used to create a
 bunch of accounts. Each account has an opening balance and a date for that
 balance. For example: your balance was EUR 100,- at the start of this year.

Once you have your accounts fixed you can start adding transactions. A
transaction is either money coming in or coming out. It has a description and
 a date as well.

If you do this for a long enough time, you have a pretty useful financial
overview. Firefly can generate charts and reports which you can use to keep
an eye on your finances.

Please read more about this on the [wiki pages](https://github.com/JC5/Firefly/wiki).

By giving such transactions extra meta-information such as a beneficiary (a
shop or something) a category and a budget you can start working on managing
your finances. But that's all extra stuff. Just add your account(s),
and start keeping track of your transactions.

## Extra's
1. You can also add transfers. Transfers can be used to move money between your
own accounts (not externals: use transactions).

2. Beneficiaries, budgets and categories can have parents and children. This is
 useful in setting up detailed transaction overviews.

3. All three of these can have limits: once you cross that limit some bars will
be red and what not.

## Prediction

Once you start entering transaction and you got about a month's worth of
data, Firefly will start to predict your expenses. Of course,
the second month will be "predicted" to be exactly the same as the first
month, but after a while it gets useful. You can remove transactions from the
predictions because they are one-time only or something.

## Caveats

Since there are no actual other users (that I know of) everything is geared
towards me using it. For me it's pretty much perfect. You will think it sucks
 or it doesn't even start or whatever. Sorry. But get in touch and I'll see
 what I can do.

# TODO
These things I have planned to implement:

1. More robust registration and logging in. It's a multi-user environment but
 when you have no e-mail sending capabilities you're pretty much out of luck.
2. More currencies (maybe in 2020).
3. Smoother user experience for new accounts. So when you finally manage to
launch this app you know what to do.
4. Write some actual installation guidelines.