/* eslint-disable @typescript-eslint/ban-types */
import { MissingOrIncorrectFieldsResponse } from '@/common/openapi/responses';
import { applyDecorators, Post as NestPost } from '@nestjs/common';
import {
  ApiBadRequestResponse,
  ApiBody,
  ApiConflictResponse,
  ApiCreatedResponse,
  ApiOperation,
} from '@nestjs/swagger';

type PostDecoratorOptions = {
  path?: string;
  name: string;
  summary: string;
  description?: string;
  bodyType: Function;
  responseType: Function | [Function];
  operationId: string;
};

export const Post = ({
  path,
  name,
  summary,
  description,
  bodyType,
  responseType,
  operationId,
}: PostDecoratorOptions) =>
  applyDecorators(
    NestPost(path ?? ''),
    ApiOperation({
      summary,
      description: description ?? `${summary}.`,
      operationId,
    }),
    ApiBody({
      description: `The ${name}'s details.`,
      type: bodyType,
    }),
    ApiCreatedResponse({
      description: `The ${name} has been successfully created.`,
      type: responseType,
    }),
    ApiConflictResponse({
      description: `Another ${name} is in conflict with this one.`,
    }),
    ApiBadRequestResponse(MissingOrIncorrectFieldsResponse),
  );
