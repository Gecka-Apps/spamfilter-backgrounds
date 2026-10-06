# spamfilter-backgrounds

Background tables for [gecka/spamfilter](https://github.com/Gecka-Apps/spamfilter):
what ordinary text looks like, one table per language, built from public
corpora. The filter uses them to tell a common word from a rare one before a
site has learned any legitimate message of its own.

The tables are published as release assets together with a `manifest.json`.
You do not clone this repository to use them:

```sh
vendor/bin/spamfilter-background fr en
```

## What a table holds

A table is 2^20 counters of 4 bytes, one per hashed feature (a word, a word
pair, a link, a number shape, a punctuation mark), gzip-compressed with a
small header. No text is stored, only counts on hashes. Most tables weigh
between 0.2 and 1 MB.

## Sources

| Source | License | Contribution |
|---|---|---|
| [Leipzig Corpora Collection](https://wortschatz.uni-leipzig.de/en/download) | CC-BY 4.0 | 300 000 sentences of news or Wikipedia text per language where available (fewer for small languages), run through the filter's own extractor: words, bigrams, casing, numbers, punctuation |
| [FrequencyWords](https://github.com/hermitdave/FrequencyWords) | MIT | the 50 000 most frequent words of OpenSubtitles 2018 for 62 languages, scaled to the sentences' mass |
| [Tatoeba](https://tatoeba.org/) | CC-BY 2.0 FR | everyday sentences, conversational and in the second person, which news text lacks; added at a quarter of the sentences' mass for the languages with at least 20 000 of them (about 30) |
| [OPUS OpenSubtitles 2018](https://opus.nlpl.eu/OpenSubtitles/corpus/version/OpenSubtitles) | corpus attribution (Lison and Tiedemann, LREC 2016); the tables hold counts on hashes, no text | the first 300 000 sentences of the monolingual file, same role and same share as Tatoeba, for the languages where Tatoeba falls short of 20 000 sentences and OPUS has a file (about 30 more) |

Leipzig Corpora Collection: D. Goldhahn, T. Eckart and U. Quasthoff, *Building
Large Monolingual Dictionaries at the Leipzig Corpora Collection: From 100 to
200 Languages*, LREC 2012.

## Building

```sh
composer install
bin/catalogue          # picks a corpus per language, writes catalogue.json
bin/build fr en de     # downloads the sources once, writes dist/<code>.bin
bin/build all          # every language, about an hour with the sources cached
bin/facts              # counts and input checksums per table, from the kept sources
bin/manifest --version=v1.3   # writes dist/manifest.json
```

Tables are named with the ISO 639-1 code when the language has one (`fr`,
`en`, `zh`), with the ISO 639-3 code otherwise (`ceb`, `hbs`).

`catalogue.json` is committed; `sources/` and `dist/` are not. The Leipzig
download site refuses automated listing, so the corpus list comes from a
dump of the Hugging Face mirror (`sources/leipzig-links.jsonl`), and newer
yearly releases are probed directly.

## Trusting the tables

A table holds a million 4-byte counters and nothing else: no code, no
text, no personal data. The filter refuses a file that claims more slots
than its ceiling, inflates past it, has the wrong length or was hashed
with another feature version, so the worst a damaged or forged table can
do is misclassify, which shows on the first messages. The download command
checks the SHA-256 of every table against the manifest and keeps nothing
that differs; `--check` reports a table that changed since. The manifest
comes from the same release as the tables, so that check guards the
transport, not the publisher. Two things do: the tables are reproducible
from public sources, below, so anyone can rebuild a release and compare;
and a detached signature of the manifest is planned once the first release
is out. To follow a release rather than `latest`, give its manifest URL
with `--manifest=`.

## Reproducing the published tables

A table is a function of its input files and of the extractor of the
filter version named in the release notes, nothing else: no randomness, no
time, no machine-specific step. The build takes the filter from a checkout
next to this repository (`../spamfilter`, a Composer path repository), so
that a change in the extractor is measured before it is released. Two builds from the same inputs give the
same bytes, and the manifest lists the SHA-256 of every table and of every
input file it was built from. To check a release rather than trust it:

```sh
git clone https://github.com/Gecka-Apps/spamfilter          # the filter, next to this repository
git -C spamfilter checkout v1.0.0                            # the filter version the release notes name
git clone https://github.com/Gecka-Apps/spamfilter-backgrounds && cd spamfilter-backgrounds
git checkout v1.3                      # the tag of the release you check
composer install                       # links ../spamfilter, a Composer path repository
curl -LO https://github.com/Gecka-Apps/spamfilter-backgrounds/releases/download/v1.3/tatoeba-sources.tar.xz
tar -xJf tatoeba-sources.tar.xz        # the Tatoeba exports used, into sources/tatoeba/
bin/build all
bin/facts && bin/manifest --version=v1.3
curl -sLo published.json https://github.com/Gecka-Apps/spamfilter-backgrounds/releases/download/v1.3/manifest.json
diff <(jq -S .languages dist/manifest.json) <(jq -S .languages published.json)
```

Where the inputs stand:

- Leipzig archives are immutable files at fixed URLs, named in
  `catalogue.json`; the build downloads them as they are.
- FrequencyWords is read at a fixed commit, written in `bin/build`.
- OPUS OpenSubtitles 2018 is a frozen corpus; the build takes the first
  300 000 lines of the monolingual file.
- Tatoeba publishes a fresh export every week and keeps no history, so the
  exports a release was built from are published with it as
  `tatoeba-sources.tar.xz` (CC-BY 2.0 FR, attribution: the Tatoeba
  contributors). Without that archive a rebuild uses the current export and
  the tables of the languages with Tatoeba sentences differ; `inputs` in
  the manifest tells you that this, and only this, is what moved.

A difference anywhere else is a bug or a tampered release: open an issue
with the two manifests.

## Publishing

```sh
bin/release v<hash-version>.<n> --dry-run   # everything but the tag, the push and the release
bin/release v<hash-version>.<n>
```

`bin/release` runs `bin/prepare-release`, which rebuilds every table from
the sources on disk, writes the facts and the manifest, checks that the
manifest describes exactly the tables in `dist/` and packs the Tatoeba
exports the tables were built from; it then tags the commit, pushes the
tag and creates the GitHub release with the tables, the manifest and the
archive. The PHP side runs wherever `php` resolves, the git and `gh` side
on the machine that runs the script. It refuses a tag for another feature
hash version than the installed filter's, an unclean working tree here or
in the filter, and a commit that is not on `origin/main` yet.

The release tag starts with the feature hash version of the filter the
tables were built for; a filter refuses tables built for another version.
The manifest names the release, the date it was generated and the commits
of this repository and of the filter it was built from, and lists the
SHA-256 of every table: `spamfilter-background --check` compares a site's
directory with it and `--update` fetches what changed.

## Contributing a language

The choice of corpus and the weights were settled on French and English,
with real contact-form submissions to measure against. For most of the
other 250 languages nobody has checked that the news corpus picked by
`bin/catalogue` reflects the language as people write it to a website, or
that a quarter of Tatoeba is the right share when Tatoeba exists at all.
If you read one of those languages, you can tell better than the tooling:

- a Leipzig corpus that fits better than the one in `catalogue.json` (a
  `mixed` or `web` crawl rather than `news`, a more recent year, a larger
  size), or another freely licensed collection of whole sentences, so that
  word pairs exist in the table;
- a sentence source in the register of correspondence, when Tatoeba has too
  few sentences for the language;
- a sign that something is wrong: everyday words that the filter flags as
  unusual, which `SpamFilter::explain()` lists with their weight.

Open an issue with the language, the source, its license and a few lines
of it, or a pull request that changes `catalogue.json` or `bin/build`. A
set of real messages in that language, spam and legitimate, is the one
thing that settles a choice; the measurement harness takes a directory of
`spam.jsonl` and `ham.jsonl`, and the messages never leave your machine.

## License

The build tooling is AGPL-3.0-or-later. The tables derive from the sources
above and carry their attribution requirements.

---
Built with 🥥 and ☕ by [Gecka](https://gecka.nc) — Kanaky-New Caledonia 🇳🇨
