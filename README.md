## How to install

### 1. Install dependencies

* [`pre-commit`](https://pre-commit.com/#install)
* [`docker`](https://docs.docker.com/get-docker)

##### Ubuntu

```bash
sudo apt install python3-pip gawk &&\
pip3 install pre-commit
```

### 2. Install the pre-commit hook globally

```bash
DIR=~/.git-template
git config --global init.templateDir ${DIR}
pre-commit init-templatedir -t pre-commit ${DIR}
```

### 3. Add configs and hooks

Step into the repository you want to have the pre-commit hooks installed and run:

```bash
git init
cat <<EOF > .pre-commit-config.yaml
repos:
- repo: git://github.com/kumadee/pre-commit-php
  rev: <VERSION> # Get the latest from: https://github.com/kumadee/pre-commit-php/releases
  hooks:
    - id: docker-phpda-0
EOF
```

### 4. Run

After pre-commit hook has been installed you can run it manually on all files in the repository

```bash
pre-commit run -a
```

## Available Hooks

There are several [pre-commit](https://pre-commit.com/) hooks to keep PHP source code in a good shape:

| Hook name                                        | Description                                                                                                                |
| ------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------- |
| `docker-phpda-0`                                 | Checks for possible dependency cycles and herarchy violations in classes.                                                  |
| `docker-phpda-1`                                 | Checks for possible dependency cycles and herarchy violations in 'Internal'.                                               |
| `docker-phpda-2`                                 | Checks for possible dependency cycles and herarchy violations in 'Domain/Framework'.                                       |
| `docker-phpda-3`                                 | Checks for possible dependency cycles and herarchy violations in 'Framework/Module'.                                       |

Check the [source file](https://github.com/kumadee/pre-commit-terraform/blob/master/.pre-commit-hooks.yaml) to know arguments used for each hook.
