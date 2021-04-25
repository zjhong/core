CREATE TABLE "public"."password_resets"
(
    "email"      varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
    "token"      varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
    "created_at" bigint DEFAULT 0                            NOT NULL
);

--
-- Name: tb_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users
(
    id                character varying(36) NOT NULL PRIMARY KEY,
    created_at        bigint DEFAULT 0      NOT NULL,
    updated_at        bigint DEFAULT 0      NOT NULL,
    enabled           boolean,
    additional_info   character varying,
    authority         character varying(255),
    customer_id       character varying(36),
    email             character varying(255),
    password          character varying(255),
    name              character varying(255),
    first_name        character varying(255),
    last_name         character varying(255),
    search_text       character varying(255),
    email_verified_at bigint DEFAULT 0      NOT NULL,
    remember_token    varchar(100)
);

-- ----------------------------
-- Uniques structure for table users
-- ----------------------------
ALTER TABLE "public"."users"
    ADD CONSTRAINT "users_email_unique" UNIQUE ("email");


--
-- Name: customer; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.customer
(
    id              character varying(36) NOT NULL PRIMARY KEY,
    additional_info character varying,
    address         character varying,
    address2        character varying,
    city            character varying(255),
    country         character varying(255),
    email           character varying(255),
    phone           character varying(255),
    search_text     character varying(255),
    state           character varying(255),
    title           character varying(255),
    zip             character varying(255)
);

--
-- Name: device; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.device
(
    id              character varying(36) NOT NULL PRIMARY KEY,
    asset_id        character varying(36),
    additional_info character varying,
    customer_id     character varying(36),
    type            character varying(255),
    name            character varying(255),
    label           character varying(255),
    search_text     character varying(255)
);

--
-- Name: device_credentials; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.device_credentials
(
    id                character varying(36) NOT NULL PRIMARY KEY,
    credentials_id    character varying,
    credentials_type  character varying(255),
    credentials_value character varying,
    device_id         character varying(36)
);

--
-- Name: ts_kv; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ts_kv
(
    entity_type character varying(255) NOT NULL,
    entity_id   character varying(36)  NOT NULL,
    key         character varying(255) NOT NULL,
    ts          bigint                 NOT NULL,
    bool_v      boolean,
    str_v       character varying(10000000),
    long_v      bigint,
    dbl_v       double precision
);

--
-- Name: ts_kv_latest; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ts_kv_latest
(
    entity_type character varying(255) NOT NULL,
    entity_id   character varying(36)  NOT NULL,
    key         character varying(255) NOT NULL,
    ts          bigint                 NOT NULL,
    bool_v      boolean,
    str_v       character varying(10000000),
    long_v      bigint,
    dbl_v       double precision
);

--
-- Name: asset; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.asset
(
    id              character varying(36) NOT NULL PRIMARY KEY,
    additional_info character varying,
    customer_id     character varying(36),
    name            character varying(255),
    label           character varying(255),
    search_text     character varying(255),
    type            character varying(255),
    parent_id       character varying(36)
);

CREATE TABLE public.asset_widget
(
    asset_id    character varying(36),
    widget_name character varying(255)
);


--
-- Name: asset; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.widget
(
    id           character varying(36) NOT NULL PRIMARY KEY,
    dashboard_id character varying(36),
    config       character varying,
    type         character varying(255),
    action        character varying(1000)
);

--
-- Name: dashboard; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.dashboard
(
    id                 character varying(36) NOT NULL PRIMARY KEY,
    configuration      character varying(10000000),
    assigned_customers character varying(1000000),
    search_text        character varying(255),
    title              character varying(255)
);
