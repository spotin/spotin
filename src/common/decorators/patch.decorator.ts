/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, Patch as NestPatch } from '@nestjs/common';
import {
  ApiBadRequestResponse,
  ApiBody,
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
} from '@nestjs/swagger';
import { MissingOrIncorrectFieldsResponse } from '../openapi/responses';

type PatchDecoratorOptions = {
  path?: string;
  name: string;
  summary: string;
  description?: string;
  bodyType: Function;
  responseType: Function | [Function];
  operationId: string;
};

export const Patch = ({
  path,
  name,
  summary,
  description,
  bodyType,
  responseType,
  operationId,
}: PatchDecoratorOptions) =>
  applyDecorators(
    NestPatch(path ?? ':id'),
    ApiOperation({
      summary,
      description: description ?? `${summary}.`,
      operationId,
    }),
    ApiParam({
      name: 'id',
      description: `The ${name} ID.`,
      format: 'uuid',
    }),
    ApiBody({
      description: `The ${name}'s details.`,
      type: bodyType,
    }),
    ApiOkResponse({
      description: `The ${name} has been successfully created.`,
      type: responseType,
    }),
    ApiNotFoundResponse({
      description: `${name} has not been found.`,
    }),
    ApiBadRequestResponse(MissingOrIncorrectFieldsResponse),
  );
