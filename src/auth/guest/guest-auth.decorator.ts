/* eslint-disable @typescript-eslint/ban-types */
import { GuestAuthGuard } from '@/auth/guest/guest-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiUnauthorizedResponse } from '@nestjs/swagger';

export const GuestAuth = (
  ...guards: (Function | CanActivate)[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    UseGuards(GuestAuthGuard, ...guards),
    ApiUnauthorizedResponse({
      description: 'Wrong access token.',
    }),
  );
